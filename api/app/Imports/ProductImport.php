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
    protected $responses = [];
    protected $processPurchaseUnit;

    public function __construct(BatchNumberService $batchNumberService, PurchaseRepository $purchaseRepository, CalculatePurchaseUnit $calculatePurchaseUnit)
    {
        $this->batchNumberService = $batchNumberService;
        $this->purchaseRepository = $purchaseRepository;
        $this->processPurchaseUnit = $calculatePurchaseUnit;
    }

    public function model(array $row)
    {
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
        $purchaseUnitData = [];

        foreach ($purchaseUnits as $index => $unitName) {
            $existingPurchaseUnit = PurchaseUnit::where('purchase_unit_name', $unitName)->first();

            if ($existingPurchaseUnit) {
                $purchaseUnitId = $existingPurchaseUnit->id;
            } else {
                $purchaseUnit = PurchaseUnit::create([
                    'purchase_unit_name' => $unitName,
                    'unit' => $units[$index],
                    'parent_purchase_unit_id' => $previousUnitId,
                    'measurement_group_id' => $measurementGroup->id,
                ]);

                $purchaseUnitId = $purchaseUnit->id;
                $previousUnitId = $purchaseUnitId;
            }

            $purchaseUnitData[] = [
                'purchase_unit_id' => $purchaseUnitId,
                'capacity_qty' => $units[$index],
            ];
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

        foreach ($purchaseUnits as $unitName) {
            $unitId = PurchaseUnit::where('purchase_unit_name', $unitName)->first()->id;

            ProductMeasurement::create([
                'product_type_id' => $productType->id,
                'purchasing_unit_id' => $unitId,
            ]);
        }

        // Retrieve product measurements to process quantity breakdown
        $productMeasurements = $productType->productMeasurement()->with('purchaseUnit')->get();

        // dd(  $productMeasurements);
        // Get the quantity breakdown
        $no_of_smallestUnit_in_each_unit = $this->processPurchaseUnit->calculatePurchaseUnits($productMeasurements);

        // dd($no_of_smallestUnit_in_each_unit);
        $quantityBreakdown = $this->processPurchaseUnit->calculateQuantityBreakdown($row['quantity'], $no_of_smallestUnit_in_each_unit);



        // Calculate the purchase and selling prices



        $highestUnit = collect($no_of_smallestUnit_in_each_unit)->sortByDesc('value')->first();
        $highestUnitCount = $highestUnit['value'];
        $highestUnitCost = trim($row['cost_price']);
        $pricePerUnit = $highestUnitCost / $highestUnit['value'];

        // Prepare the quantity breakdown based on the no_of_smallestUnit_in_each_unit structure
        $quantityBreakdown = [];
        foreach ($no_of_smallestUnit_in_each_unit as $unitName => $data) {
            $unitCount = $data['value'];
            $costPrice = $pricePerUnit * $unitCount;
            $sellingPrice = $costPrice * 1.1; // Add 10% for selling price

            $quantityBreakdown[] = [
                'unit' => $unitName,
                'count' => $unitCount,
                'purchase_unit_id' => $data['purchase_unit_id'],
                'cost_price' => $costPrice,
                'selling_price' => $sellingPrice,
            ];
        }
        $supplier = User::where('first_name', 'No supplier')->firstOrFail();

        $purchaseData = [
            'product_type_id' => $productType->id,
            'batch_no' => $newBatchNumber,
            'supplier_id' => $supplier->id,
            'product_identifier' => '',
            'expiry_date' => !empty($row['expiry_date']) ? \DateTime::createFromFormat('d/m/Y', trim($row['expiry_date']))->format('Y-m-d') : null,
            'purchase_unit_data' => $quantityBreakdown, // Includes cost_price and selling_price
        ];

        $response = $this->purchaseRepository->create(['purchases' => [$purchaseData]]);

        $responseContent = $response->getContent();
        $responseData = json_decode($responseContent, true);

        if (!$responseData['message']) {
            throw new \Exception('Purchase creation failed.');
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
