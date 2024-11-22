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
        // Retrieve the category and subcategory based on names
        $category = ProductCategory::where('category_name', trim($row['category_name']))->first();
        $subCategory = ProductSubCategory::where('sub_category_name', trim($row['sub_category_name']))->first();

        // Retrieve the selling unit capacity based on piece_name
        // if (isset($row['piece_name']) && !empty(trim($row['piece_name']))) {
        //     $sellingUnitCapacity = SellingUnitCapacity::where('piece_name', trim($row['piece_name']))->first();
        // } else {
        //     $sellingUnitCapacity = null; // Or handle the case where 'piece_name' is not set
        // }

        $newBatchNumber = $this->batchNumberService->generateBatchNumber();
        $supplier = User::select("id")->where('first_name', "No supplier")->first();

        // if (!$category || !$subCategory || !$sellingUnitCapacity) {

        //     return null;
        // }


        // Retrieve the selling unit and purchase unit
        // $sellingUnit = $sellingUnitCapacity->sellingUnit;
        // $purchaseUnit = $sellingUnit->purchaseUnit;

        $sellingUnitCapacity = 0;
        $sellingUnit = 0;
        $purchaseUnit = 0;
        // Convert VAT value to 0 or 1
        $vatValue = strtolower(trim($row['vat'])) === 'yes' ? 1 : 0;
        //check for batch no
        $newBatchNumber = isset($row['batch_no']) && !empty(trim($row['batch_no'])) ? trim($row['batch_no']) : $this->batchNumberService->generateBatchNumber();

        $productType = null;

        DB::transaction(function () use ($row, $category, $subCategory, $sellingUnitCapacity, $sellingUnit, $purchaseUnit, $vatValue, $newBatchNumber, $supplier) {
            //dd($row);
            try {
                $productType = new ProductType([
                     'product_type_name' => Str::limit(trim($row['product_type_name']), 50),
                     'product_type_description' => Str::limit(trim($row['product_type_description']), 200),
                     'product_type_image' => null, // You might need to handle image uploading separately
                     'vat' => $vatValue,
                     'sub_category_id' => $subCategory->id,
                     'category_id' => $category->id,
                    //  'selling_unit_capacity_id' => 10,
                    //  'selling_unit_id' => 10,
                    //  'purchase_unit_id' => 10,
                     'barcode' => Str::limit(trim($row['barcode']), 200),
                     // 'created_by' and 'updated_by' fields should be set based on your application logic
                     // 'created_by' => ?,
                     // 'updated_by' => ?,
                 ]);
                $productType->save();

                $purchaseData = [
                    'product_type_id' => $productType->id,
                    'capacity_qty' => trim($row['capacity_qty']),
                    'expiry_date' => !empty($row['expiry_date']) ? \DateTime::createFromFormat('d/m/Y', trim($row['expiry_date']))->format('Y-m-d') : null,
                    'batch_no' => $newBatchNumber,
                    'supplier_id' => $supplier->id,
                    'product_identifier' => '',
                    'cost_price' => trim($row['cost_price']),
                    'selling_price' => trim($row['selling_price']),
                ];

                $response = $this->purchaseRepository->create(['purchases' => [$purchaseData]]);

                $responseContent = $response->getContent();
                $responseData = json_decode($responseContent, true); // Decode as an associative array



                if (!$responseData['state']) {
                    $this->responses = [

                        'message' => 'error',
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
                // Rollback and log the error if any exception occurs
                DB::rollBack();
                \Log::error('Transaction rolled back due to error: ' . $e->getMessage());
                // If the state is false, rollback the transaction manually
                $this->responses = [

                   'message' => $e->getMessage(),
                   'success' => false,
                ];
                return response()->json(['message' => 'Transaction failed: ' . $e->getMessage()], 500);
            }
        });

        //return $productType;
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
