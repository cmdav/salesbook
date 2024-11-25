<?php

namespace App\Imports;

use App\Models\ProductType;
use App\Models\ProductCategory;
use App\Models\ProductSubCategory;
use App\Models\SellingUnitCapacity;
use App\Models\SellingUnit;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Illuminate\Support\Str;
use App\Services\BatchNumberService;
use App\Services\Inventory\PurchaseService\PurchaseRepository;

class ProductImport implements ToModel, WithHeadingRow, WithValidation, SkipsEmptyRows
{
    protected $batchNumberService;
    protected $purchaseRepository;
    protected $responses = [];


    public function __construct(BatchNumberService $batchNumberService, PurchaseRepository $purchaseRepository)
    {
        $this->batchNumberService = $batchNumberService;
        $this->purchaseRepository = $purchaseRepository;
    }

    public function model(array $row)
    {
        DB::transaction(function () use ($row) {
            try {
                // Retrieve the category and subcategory based on names
                $category = ProductCategory::where('category_name', trim($row['category_name']))->first();
                $subCategory = ProductSubCategory::where('sub_category_name', trim($row['sub_category_name']))->first();

                // Retrieve or create the purchase unit
                // Retrieve or create the purchase unit and return the ID
                $purchaseUnit = \App\Models\PurchaseUnit::firstOrCreate(
                    ['purchase_unit_name' => trim($row['purchase_unit'])],
                    ['id' => (string) \Illuminate\Support\Str::uuid()]
                );

                $purchaseUnitId = $purchaseUnit->id; // Extract the ID explicitly

                // Retrieve or create the selling unit and return the ID
                $sellingUnit = \App\Models\SellingUnit::firstOrCreate(
                    [
                        'selling_unit_name' => trim($row['selling_unit']),
                        'purchase_unit_id' => $purchaseUnitId,
                    ],
                    ['id' => (string) \Illuminate\Support\Str::uuid()]
                );

                $sellingUnitId = $sellingUnit->id; // Extract the ID explicitly

                // Retrieve or create the selling unit capacity and return the ID (integer)
                $sellingUnitCapacity = \App\Models\SellingUnitCapacity::firstOrCreate(
                    [
                        'selling_unit_id' => $sellingUnitId,
                        'selling_unit_capacity' => intval($row['capacity_qty']),
                    ]
                );

                $sellingUnitCapacityId = $sellingUnitCapacity->id; // Extract the ID explicitly
                // Convert VAT value to 0 or 1
                $vatValue = strtolower(trim($row['vat'])) === 'yes' ? 1 : 0;

                // Check for or generate a batch number
                $newBatchNumber = isset($row['batch_no']) && !empty(trim($row['batch_no']))
                    ? trim($row['batch_no'])
                    : $this->batchNumberService->generateBatchNumber();

                // Create the product type
                $productType = new ProductType([
                    'product_type_name' => Str::limit(trim($row['product_type_name']), 50),
                    'product_type_description' => Str::limit(trim($row['product_type_description']), 200),
                    'product_type_image' => null, // You might need to handle image uploading separately
                    'vat' => $vatValue,
                    'sub_category_id' => $subCategory->id,
                    'category_id' => $category->id,
                    'barcode' => Str::limit(trim($row['barcode']), 200),
                    'purchase_unit_id' => $purchaseUnit->id,
                    'selling_unit_id' => $sellingUnit->id,
                    'selling_unit_capacity_id' => $sellingUnitCapacity->id,
                ]);
                $productType->save();

                // Prepare purchase data
                $purchaseData = [
                    'product_type_id' => $productType->id,
                    'capacity_qty' => intval(trim($row['capacity_qty'])) * intval(trim($row['no_of_pieces'])),
                    'expiry_date' => !empty($row['expiry_date'])
                        ? \DateTime::createFromFormat('d/m/Y', trim($row['expiry_date']))->format('Y-m-d')
                        : null,
                    'batch_no' => $newBatchNumber,
                    'supplier_id' => User::select("id")->where('first_name', "No supplier")->first()->id,
                    'product_identifier' => '',
                    'cost_price' => trim($row['cost_price']),
                    'selling_price' => trim($row['selling_price']),
                ];

                // Insert purchase data using the repository
                $response = $this->purchaseRepository->create(['purchases' => [$purchaseData]]);

                $responseContent = $response->getContent();
                $responseData = json_decode($responseContent, true);

                if (!$responseData) {
                    $this->responses = [
                        'message' => 'Error importing row',
                        'success' => false,
                    ];
                    throw new \Exception('Purchase creation failed. Rolling back transaction.');
                } else {
                    $this->responses = [
                        'message' => 'Row imported successfully',
                        'success' => true,
                    ];
                }
            } catch (\Exception $e) {
                DB::rollBack();
                \Log::error('Transaction rolled back due to error: ' . $e->getMessage());
                $this->responses[] = [
                    'message' => $e->getMessage(),
                    'success' => false,
                ];
            }
        });
    }


    public function rules(): array
    {
        return [
            'product_type_name' => 'required|string|max:250|unique:product_types|regex:/^[^\s]/',
            'product_type_description' => 'required|string|max:200',
            'category_name' => 'nullable|string|exists:product_categories,category_name',
            'sub_category_name' => 'nullable|string|exists:product_sub_categories,sub_category_name',
            'vat' => 'required|string|in:yes,no',
            'capacity_qty' => 'required|numeric|min:1',
             'expiry_date' => 'nullable|regex:/^\d{1,2}\/\d{1,2}\/\d{2,4}$/',
            'batch_no' => 'nullable|max:50',
            'cost_price' => 'required|integer|min:1',
            'selling_price' => 'required|integer|min:1',
            'no_of_pieces' => 'required|integer|min:1',
            'purchase_unit' => 'required',
            'selling_unit' => 'required',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'category_name.exists' => 'The specified product category does not exist.',
            'sub_category_name.exists' => 'The specified product subcategory does not exist.',
            'vat.in' => 'The VAT field must be either "yes" or "no".',
            'expiry_date' => 'Expiry date format should be dd/mm/year',
        ];
    }
    public function getResponses()
    {
        return $this->responses;
    }
}
