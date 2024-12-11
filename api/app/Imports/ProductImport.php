<?php

namespace App\Imports;

use App\Models\ProductType;
use App\Models\ProductCategory;
use App\Models\ProductSubCategory;
use App\Models\PurchaseUnit;
use App\Models\MeasurementGroup;
use App\Models\ProductMeasurement;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Illuminate\Support\Str;
use App\Services\BatchNumberService;
use App\Services\Inventory\PurchaseService\PurchaseRepository;
use App\Services\CalculatePurchaseUnit;

class ProductImport implements ToModel, WithHeadingRow, WithValidation, SkipsEmptyRows
{
    protected $batchNumberService;
    protected $purchaseRepository;
    protected $responses;

    public function __construct(BatchNumberService $batchNumberService, PurchaseRepository $purchaseRepository)
    {
        $this->batchNumberService = $batchNumberService;
        $this->purchaseRepository = $purchaseRepository;
    }

    public function model(array $row)
    {
        DB::beginTransaction(); // Start the transaction

        try {
            $category = ProductCategory::where('category_name', trim($row['category_name']))->first();
            $subCategory = ProductSubCategory::where('sub_category_name', trim($row['sub_category_name']))->first();

            $groupName = trim($row['group']);
            $measurementGroup = MeasurementGroup::firstOrCreate(['group_name' => $groupName], ['created_by' => auth()->id()]);

            $purchaseUnits = array_map('trim', explode(',', $row['purchase_unit']));
            $units = array_map('trim', explode(',', $row['unit']));

            if (count($purchaseUnits) !== count($units)) {
                throw new \Exception('The number of purchase units does not match the number of unit values.');
            }

            $previousUnitId = null;

            foreach ($purchaseUnits as $index => $unitName) {
                $existingPurchaseUnit = PurchaseUnit::where('purchase_unit_name', $unitName)->first();

                if (!$existingPurchaseUnit) {
                    $purchaseUnit = PurchaseUnit::create([
                        'purchase_unit_name' => $unitName,
                        'unit' => $units[$index],
                        'parent_purchase_unit_id' => $previousUnitId,
                        'measurement_group_id' => $measurementGroup->id,
                    ]);

                    $previousUnitId = $purchaseUnit->id;
                }
            }

            $newBatchNumber = isset($row['batch_no']) && !empty(trim($row['batch_no'])) ? trim($row['batch_no']) : $this->batchNumberService->generateBatchNumber();
            $vatValue = strtolower(trim($row['vat'])) === 'yes' ? 1 : 0;

            $productType = ProductType::create([
                'product_type_name' => Str::limit(trim($row['product_type_name']), 50),
                'product_type_description' => Str::limit(trim($row['product_type_description']), 200),
                'vat' => $vatValue,
                'sub_category_id' => optional($subCategory)->id,
                'category_id' => optional($category)->id,
                'barcode' => Str::limit(trim($row['barcode']), 200),
            ]);

            foreach ($purchaseUnits as $index => $unitName) {
                $unitId = PurchaseUnit::where('purchase_unit_name', $unitName)->first()->id;

                ProductMeasurement::create([
                    'product_type_id' => $productType->id,
                    'purchasing_unit_id' => $unitId,
                ]);
            }

            // Calculate purchase and selling prices based on the provided logic
            $costPrice = (float) trim($row['cost_price']); // Cost price for the highest unit
            $quantityBreakdown = [];

            foreach ($units as $index => $unitCount) {
                if ($index === 0) {
                    // Highest unit retains the original cost price
                    $pricePerUnit = $costPrice;
                } else {
                    // Divide the previous price by the current unit count
                    $pricePerUnit = $quantityBreakdown[$index - 1]['cost_price'] / $unitCount;
                }

                $sellingPrice = round($pricePerUnit * 1.1, 2); // Add 10% for selling price and round to 2 decimals

                $quantityBreakdown[] = [
                    'unit' => $purchaseUnits[$index],
                    'count' => $unitCount,
                    'purchase_unit_id' => PurchaseUnit::where('purchase_unit_name', $purchaseUnits[$index])->first()->id,
                    'cost_price' => round($pricePerUnit, 2), // Round to 2 decimals for clarity
                    'selling_price' => $sellingPrice,
                    'capacity_qty' => $row['quantity'],
                ];
            }

            $supplier = User::where('first_name', 'No supplier')->firstOrFail();

            $purchaseData = [
                'product_type_id' => $productType->id,
                'batch_no' => $newBatchNumber,
                'supplier_id' => $supplier->id,
                'product_identifier' => '',
                'expiry_date' => !empty($row['expiry_date']) ? \DateTime::createFromFormat('d/m/Y', trim($row['expiry_date']))->format('Y-m-d') : null,
                'purchase_unit_data' => array_reverse($quantityBreakdown), // Reverse back to match the hierarchy
            ];

            // Save purchase data using repository
            $this->purchaseRepository->create(['purchases' => [$purchaseData]]);

            DB::commit(); // Commit the transaction
        } catch (\Exception $e) {
            DB::rollBack(); // Rollback the transaction in case of error
            // Log the error for debugging purposes
            \Log::error('Product import failed: ' . $e->getMessage(), [
                'row' => $row,
                'stack' => $e->getTraceAsString(),
            ]);

            // Optionally, rethrow the exception if needed
            throw new \Exception('Product import failed: ' . $e->getMessage());
        }
    }


    public function rules(): array
    {
        return [
            'product_type_name' => 'required|string|max:250|unique:product_types|regex:/^[^\s]/',
            'product_type_description' => 'nullable|string|max:200',
            'category_name' => 'nullable|string|exists:product_categories,category_name',
            'sub_category_name' => 'nullable|string|exists:product_sub_categories,sub_category_name',
            'vat' => 'required|string|in:yes,no',
            'quantity' => 'required|numeric|min:1',
            'expiry_date' => 'nullable|regex:/^\d{1,2}\/\d{1,2}\/\d{2,4}$/',
            'batch_no' => 'nullable|max:50',
            'cost_price' => 'required|integer|min:1',
            'purchase_unit' => 'required|string',
            'unit' => 'required|string',
            'group' => 'required|string',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'category_name.exists' => 'The specified product category does not exist.',
            'sub_category_name.exists' => 'The specified product subcategory does not exist.',
            'vat.in' => 'The VAT field must be either "yes" or "no".',
            'expiry_date.regex' => 'Expiry date format should be dd/mm/yyyy.',
            'purchase_unit.required' => 'Purchase units are required.',
            'unit.required' => 'Units are required.',
            'group.required' => 'Measurement group is required.',
        ];
    }

    public function getResponses()
    {
        return $this->responses;
    }
}
