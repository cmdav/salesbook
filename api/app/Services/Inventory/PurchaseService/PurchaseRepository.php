<?php

namespace App\Services\Inventory\PurchaseService;

use App\Models\Purchase;
use App\Models\SupplierRequest;
use App\Models\Inventory;
use App\Models\Sale;
use App\Models\Price;
use App\Models\Store;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use App\Services\Security\LogService\LogRepository;

class PurchaseRepository
{
    protected $logRepository;
    protected $username;

    public function __construct(LogRepository $logRepository)
    {
        $this->logRepository = $logRepository;
        $this->username = $this->logRepository->getUsername();
    }

    private function query($branchId)
    {

        $query = Purchase::with([
            'suppliers:id,first_name,last_name',
            'currency',
            'productType:id,product_type_name,product_type_image,product_type_description',
            'productType.productMeasurement.sellingUnitCapacity:id,selling_unit_id,selling_unit_capacity',
            'productType.productMeasurement.sellingUnitCapacity.sellingUnit:id,selling_unit_name,purchase_unit_id',
            'productType.productMeasurement.sellingUnitCapacity.sellingUnit.purchaseUnit:id,purchase_unit_name',
            'productType.subCategory:id,sub_category_name',
            'branches:id,name',
            'productType.suppliers:id,first_name,last_name,phone_number',
            'productType.activePrice' => function ($query) {
                $query->select('id', 'cost_price', 'selling_price', 'product_type_id');
            }
        ]);


        if ($branchId !== 'all') {
            $query->where('branch_id', $branchId);
        }

        return $query->latest();

    }

    public function index($request)
    {
        $this->logRepository->logEvent(
            'purchases',
            'view',
            null,
            'Purchase',
            "$this->username viewed all purchases"
        );

        $branchId = 'all';
        if(isset($request['branch_id']) &&  auth()->user()->role->role_name == 'Admin') {
            $branchId = $request['branch_id'];
        } elseif (!in_array(auth()->user()->role->role_name, ['Admin', 'Super Admin'])) {
            $branchId = auth()->user()->branch_id;
        }


        $Purchase = $this->query($branchId)->paginate(20);

        $Purchase->getCollection()->transform(function ($Purchase) {
            return $this->transformProduct($Purchase);
        });

        return $Purchase;
    }

    public function searchPurchase($searchCriteria, $request)
    {
        $this->logRepository->logEvent(
            'purchases',
            'search',
            null,
            'Purchase',
            "$this->username searched for purchases with criteria: $searchCriteria"
        );

        $branchId = 'all';
        if(isset($request['branch_id']) &&  auth()->user()->role->role_name == 'Admin') {
            $branchId = $request['branch_id'];
        } elseif (!in_array(auth()->user()->role->role_name, ['Admin', 'Super Admin'])) {
            $branchId = auth()->user()->branch_id;
        }
        $Purchase = $this->query($branchId)
        ->where(function ($query) use ($searchCriteria) {
            $query->whereHas('productType', function ($q) use ($searchCriteria) {
                $q->where('product_type_name', 'like', '%' . $searchCriteria . '%');
            });
        })->get();

        $Purchase->transform(function ($Purchase) {

            return $this->transformProduct($Purchase);
        });
        return $Purchase;


    }
    private function transformProduct($purchase)
    {
        // Get cost price and selling price
        $cost_price = $purchase->price ? $purchase->price->cost_price : 0;
        $formatted_cost_price = number_format($cost_price, 2, '.', ',');

        $selling_price = $purchase->price ? $purchase->price->selling_price : 0;
        $formatted_selling_price = number_format($selling_price, 2, '.', ',');

        // Initialize variables for purchase unit names, selling unit names, and capacities
        $purchaseUnitNames = [];
        $sellingUnitNames = [];
        $sellingUnitCapacities = [];

        // Check if productType exists and populate unit arrays
        if ($purchase->productType) {
            foreach ($purchase->productType->productMeasurement as $measurement) {
                $purchaseUnitNames[] = optional($measurement->sellingUnitCapacity->sellingUnit->purchaseUnit)->purchase_unit_name;
                $sellingUnitNames[] = optional($measurement->sellingUnitCapacity->sellingUnit)->selling_unit_name;
                $sellingUnitCapacities[] = optional($measurement->sellingUnitCapacity)->selling_unit_capacity;
            }

            // Remove duplicates
            $purchaseUnitNames = array_unique($purchaseUnitNames);
            $sellingUnitNames = array_unique($sellingUnitNames);
            $sellingUnitCapacities = array_unique($sellingUnitCapacities);
        }

        return [
            'id' => $purchase->id,
            'product_type_name' => optional($purchase->productType)->product_type_name,
            'product_type_image' => optional($purchase->productType)->product_type_image,
            'product_type_description' => optional($purchase->productType)->product_type_description,

            // Separate keys for each unit type
            'purchase_unit_name' => $purchaseUnitNames,
            'selling_unit_name' => $sellingUnitNames,
            'selling_unit_capacity' => $sellingUnitCapacities,

            'capacity_qty' => $purchase->capacity_qty,
            'branch_name' => optional($purchase->branches)->name,
            'batch_no' => $purchase->batch_no,
            'quantity' => $purchase->quantity,
            'expiry_date' => $purchase->expiry_date,
            'cost_price' => $formatted_cost_price,
            'selling_price' => $formatted_selling_price,
            'supplier' => optional($purchase->suppliers)->first_name . " " . optional($purchase->suppliers)->last_name,
            'created_by' => optional($purchase->creator)->first_name
                ? optional($purchase->creator)->first_name . " " . optional($purchase->creator)->last_name
                : optional($purchase->creator->organization)->organization_name,

            'updated_by' => optional($purchase->updater)->first_name
                ? optional($purchase->updater)->first_name . " " . optional($purchase->updater)->last_name
                : optional($purchase->updater->organization)->organization_name,
        ];
    }



    public function create(array $data)
    {

        DB::beginTransaction();

        //try {
        $purchases = [];

        foreach ($data['purchases'] as $purchaseData) {
            // Create the Purchase instance (only one per purchaseData)
            $purchase = new Purchase();
            $purchase->product_type_id = $purchaseData['product_type_id'];
            $purchase->supplier_id = $purchaseData['supplier_id'];
            $purchase->batch_no = $purchaseData['batch_no'];
            $purchase->purchase_unit_id = $purchaseData['purchase_unit_id'];
            $purchase->product_identifier = $purchaseData['product_identifier'];
            $purchase->expiry_date = $purchaseData['expiry_date'] ?? null;
            $purchase->capacity_qty = $purchaseData['capacity_qty'] ?? 0;

            // Retrieve product type with related selling and purchase units
            $productType = \App\Models\ProductType::with([
                'productMeasurement.sellingUnitCapacity:id,selling_unit_id,selling_unit_capacity',
                'productMeasurement.sellingUnitCapacity.sellingUnit:id,selling_unit_name,purchase_unit_id',
            ])->find($purchaseData['product_type_id']);

            // Convert capacity_qty if required based on selling unit capacity
            // foreach ($productType->productMeasurement as $measurement) {
            //     if ($measurement->sellingUnitCapacity && $measurement->sellingUnitCapacity->selling_unit_id === $purchaseData['purchase_unit_id']) {
            //         $purchase->capacity_qty *= $measurement->sellingUnitCapacity->selling_unit_capacity;
            //         break;
            //     }
            // }

            $purchase->save(); // Save the purchase instance once

            foreach ($purchaseData['selling_unit_data'] as $unitData) {
                // Create or update the Price instance for each selling unit
                $price = new Price();
                $price->product_type_id = $purchaseData['product_type_id'];
                $price->supplier_id = $purchaseData['supplier_id'];
                $price->batch_no = $purchaseData['batch_no'];
                $price->purchase_unit_id = $purchaseData['purchase_unit_id'];
                $price->selling_unit_id = $unitData['selling_unit_id'];
                $price->status = 1;

                if (!empty($unitData['price_id'])) {
                    $price->price_id = $unitData['price_id'];
                } else {
                    $price->cost_price = $unitData['cost_price'] ?? null;
                    $price->selling_price = $unitData['selling_price'] ?? null;
                }
                $price->save();
            }

            // Update or create the Store instance
            $store = \App\Models\Store::where('product_type_id', $purchaseData['product_type_id'])
                ->where('batch_no', $purchaseData['batch_no'])
                ->where('purchase_unit_id', $purchaseData['purchase_unit_id'])
                ->where('branch_id', auth()->user()->branch_id)
                ->first();

            if (!$store) {
                // Create a new Store instance if it doesn't exist
                $store = new \App\Models\Store();
                $store->product_type_id = $purchaseData['product_type_id'];
                $store->batch_no = $purchaseData['batch_no'];
                $store->purchase_unit_id = $purchaseData['purchase_unit_id'];
                $store->branch_id = auth()->user()->branch_id;
                $store->capacity_qty_available = 0;
            }

            // Update the store capacity with the adjusted quantity
            $store->capacity_qty_available += $purchase->capacity_qty;
            $store->save();

            $purchases[] = $purchase; // Add the purchase to the array
        }
        $this->logRepository->logEvent(
            'purchases',
            'create',
            null,
            'Purchase',
            "$this->username created purchases",
            $data
        );
        DB::commit();
        return response()->json(['data' => $purchases, 'message' => 'Purchase record was added successfully'], 201);
        // } catch (\Exception $e) {
        //     Log::channel('insertion_errors')->error('Error creating or updating user: ' . $e->getMessage());
        //     DB::rollBack();
        //     return response()->json(['message' => 'Failed to create purchases'], 500);
        // }
    }






    public function findById($id)
    {
        return Purchase::find($id);
    }

    public function delete($id)
    {
        $purchase = Purchase::find($id);
        try {
            if ($purchase) {
                // Delete matching entries in the Store table
                Store::where('product_type_id', $purchase->product_type_id)
                    ->where('batch_no', $purchase->batch_no)
                    ->delete();

                $purchase->delete();
                $this->logRepository->logEvent(
                    'purchases',
                    'delete',
                    $id,
                    'Purchase',
                    "$this->username deleted purchase with ID $id"
                );

                return response()->json([
                    'success' => true,
                    'message' => 'Deletion successful',
                ], 200);
            }

        } catch (QueryException $e) {
            // This will catch SQL constraint violations or other query-related errors
            Log::channel('insertion_errors')->error('Error deleting purchase: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'This Purchase is already in use',
            ], 500);

        }
    }



}
