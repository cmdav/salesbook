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

class PurchaseRepository
{
    private function query($branchId)
    {

        $query = Purchase::with([
            'suppliers:id,first_name,last_name',
            'currency',
            'productType:id,product_type_name,product_type_image,product_type_description,selling_unit_capacity_id,purchase_unit_id',
            'productType.sellingUnitCapacity:id,selling_unit_id,selling_unit_capacity',
            'productType.unitPurchase:id,purchase_unit_name',
            'productType.sellingUnit' => function ($q) {
                $q->select('selling_units.id', 'selling_units.purchase_unit_id', 'selling_units.selling_unit_name');
            },

            'branches:id,name',
            // 'productType.containerCapacities:id,container_type_id,container_capacity',
            // 'productType.containertype:id,container_type_name'
        ]);

        if ($branchId !== 'all') {
            $query->where('branch_id', $branchId);
        }

        return $query->latest();

    }

    public function index($request)
    {


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
        // $purchase is the purchase data returned from the API
        $cost_price = $purchase->price ? $purchase->price->cost_price : 0;
        $formatted_cost_price = number_format($cost_price, 2, '.', ',');

        $selling_price = $purchase->price ? $purchase->price->selling_price : 0;
        $formatted_selling_price = number_format($selling_price, 2, '.', ',');

        // Initialize variables
        $containerTypeName = null;
        // $containerCapacity = null;

        // Check if productType exists
        if ($purchase->productType) {
            // Access the container type name directly from the containertype relationship
            $containerTypeName = optional($purchase->productType->containertype)->container_type_name;
        }

        return [
            'id' => $purchase->id,
            'product_type_name' => optional($purchase->productType)->product_type_name,
            'product_type_image' => optional($purchase->productType)->product_type_image,
            'product_type_description' => optional($purchase->productType)->product_type_description,

            'selling_unit_capacity' => optional($purchase->productType->sellingUnitCapacity)->selling_unit_capacity,
            'selling_unit_name' => optional($purchase->productType->sellingUnit)->selling_unit_name,
            'purchase_unit_name' => optional($purchase->productType->unitPurchase)->purchase_unit_name,

            'capacity_qty' => $purchase->capacity_qty,
            'branch_name' => optional($purchase->branches)->name,
            'batch_no' => $purchase->batch_no,
            'quantity' => $purchase->quantity,
            'expiry_date' => $purchase->expiry_date,
            'cost_price' => $formatted_cost_price,
            'selling_price' => $formatted_selling_price,
            'supplier' => optional($purchase->suppliers)->first_name . " " . optional($purchase->suppliers)->last_name,
            'created_by' => optional($purchase->creator)->first_name ? optional($purchase->creator)->first_name . " " . optional($purchase->creator)->last_name : optional($purchase->creator->organization)->organization_name,

            'updated_by' => optional($purchase->updater)->first_name ? optional($purchase->updater)->first_name . " " . optional($purchase->updater)->last_name : optional($purchase->updater->organization)->organization_name,

            //'updated_by' => optional($purchase->updater)->fullname,
        ];
    }

    public function create(array $data)
    {
        DB::beginTransaction();

        try {
            $purchases = [];

            foreach ($data['purchases'] as $purchaseData) {
                foreach ($purchaseData['selling_unit_data'] as $unitData) {
                    // Create a new Price instance
                    $price = new Price();
                    $price->product_type_id = $purchaseData['product_type_id'];
                    $price->supplier_id = $purchaseData['supplier_id'];
                    $price->batch_no = $purchaseData['batch_no'];
                    $price->purchase_unit_id = $purchaseData['purchase_unit_id'];
                    $price->selling_unit_id = $unitData['selling_unit_id'];
                    $price->status = 1;

                    // If price_id is provided, use it directly; otherwise, set cost_price and selling_price
                    if (!empty($unitData['price_id'])) {
                        $price->price_id = $unitData['price_id'];
                    } else {
                        $price->cost_price = $unitData['cost_price'] ?? null;
                        $price->selling_price = $unitData['selling_price'] ?? null;
                    }
                    $price->save();

                    // Store the price_id for subsequent use
                    $unitData['price_id'] = $price->id;
                }

                // Insert into SupplierProduct table if new supplier entry
                if (!empty($purchaseData['supplier_id'])) {
                    $existingRecord = \App\Models\SupplierProduct::where('product_type_id', $purchaseData['product_type_id'])
                        ->where('supplier_id', $purchaseData['supplier_id'])
                        ->first();
                    if (!$existingRecord) {
                        $supplierProduct = new \App\Models\SupplierProduct();
                        $supplierProduct->product_type_id = $purchaseData['product_type_id'];
                        $supplierProduct->supplier_id = $purchaseData['supplier_id'];
                        $supplierProduct->save();
                    }
                }

                // Create a new Purchase instance
                $purchase = new Purchase();
                $purchase->product_type_id = $purchaseData['product_type_id'];
                $purchase->supplier_id = $purchaseData['supplier_id'];
                $purchase->price_id = $unitData['price_id'];  // Reference the last price ID in the unitData loop
                $purchase->batch_no = $purchaseData['batch_no'];
                $purchase->purchase_unit_id = $purchaseData['purchase_unit_id'];
                $purchase->product_identifier = $purchaseData['product_identifier'];
                $purchase->expiry_date = $purchaseData['expiry_date'] ?? null;
                $purchase->capacity_qty = $purchaseData['capacity_qty'] ?? 0;
                $purchase->save();

                $purchases[] = $purchase;
            }

            DB::commit();

            return response()->json(['data' => $purchases, 'message' => 'Purchase record was added successfully', 'state' => true], 201);
        } catch (\Exception $e) {
            Log::channel('insertion_errors')->error('Error creating or updating user: ' . $e->getMessage());
            DB::rollBack();
            return response()->json(['message' => 'Failed to create purchases', 'state' => false], 500);
        }
    }
    // public function create(array $data)
    // {
    //     DB::beginTransaction();

    //     try {
    //         $purchases = [];

    //         foreach ($data['purchases'] as $purchaseData) {
    //             // Create a new Price instance
    //             $price = new Price();
    //             $price->product_type_id = $purchaseData['product_type_id'];
    //             $price->supplier_id = $purchaseData['supplier_id'];
    //             $price->batch_no = $purchaseData['batch_no'];
    //             $price->purchase_unit_id = $purchaseData['purchase_unit_id'];
    //             $price->status = 1;

    //             // If price_id is empty, this is the initial price, so set cost and selling prices
    //             if (empty($purchaseData['price_id'])) {
    //                 $price->cost_price = $purchaseData['cost_price'];
    //                 $price->selling_price = $purchaseData['selling_price'];
    //                 $price->save();

    //                 $purchaseData['price_id'] = $price->id;
    //             } else {
    //                 // Else, set the price_id
    //                 $price->price_id = $purchaseData['price_id'];
    //                 $price->save();
    //             }

    //             // Check and save new supplier into supplier_product table
    //             if (!empty($purchaseData['supplier_id'])) {
    //                 $existingRecord = \App\Models\SupplierProduct::where('product_type_id', $purchaseData['product_type_id'])
    //                     ->where('supplier_id', $purchaseData['supplier_id'])
    //                     ->first();
    //                 if (!$existingRecord) {
    //                     $supplierProduct = new \App\Models\SupplierProduct();
    //                     $supplierProduct->product_type_id = $purchaseData['product_type_id'];
    //                     $supplierProduct->supplier_id = $purchaseData['supplier_id'];
    //                     $supplierProduct->save();
    //                 }
    //             }

    //             // Create a new Purchase instance
    //             $purchase = new Purchase();
    //             $purchase->product_type_id = $purchaseData['product_type_id'];
    //             $purchase->supplier_id = $purchaseData['supplier_id'];
    //             $purchase->price_id = $purchaseData['price_id'];
    //             $purchase->batch_no = $purchaseData['batch_no'];
    //             $purchase->purchase_unit_id = $purchaseData['purchase_unit_id'];
    //             $purchase->product_identifier = $purchaseData['product_identifier'];
    //             $purchase->expiry_date = isset($purchaseData['expiry_date']) && !empty($purchaseData['expiry_date']) ? $purchaseData['expiry_date'] : null;
    //             $purchase->capacity_qty = $purchaseData['capacity_qty'];
    //             $purchase->save();

    //             // Retrieve product type with related selling and purchase units
    //             $productType = \App\Models\ProductType::with([
    //                 'productMeasurement.sellingUnitCapacity:id,selling_unit_id,selling_unit_capacity',
    //                 'productMeasurement.sellingUnitCapacity.sellingUnit:id,selling_unit_name,purchase_unit_id',
    //                 'productMeasurement.sellingUnitCapacity.sellingUnit.purchaseUnit:id,purchase_unit_name',
    //             ])->find($purchaseData['product_type_id']);

    //             // Multiply capacity_qty with each selling_unit_capacity for each measurement
    //             foreach ($productType->productMeasurement as $measurement) {
    //                 $purchaseData['capacity_qty'] *= optional($measurement->sellingUnitCapacity)->selling_unit_capacity;
    //             }

    //             // Check if the store already exists for the specific branch
    //             $store = \App\Models\Store::where('product_type_id', $purchaseData['product_type_id'])
    //                 ->where('batch_no', $purchaseData['batch_no'])
    //                 ->where('branch_id', auth()->user()->branch_id)
    //                 ->first();

    //             if (!$store) {
    //                 // Create a new Store instance if not exists
    //                 $store = new \App\Models\Store();
    //                 $store->product_type_id = $purchaseData['product_type_id'];
    //                 $store->batch_no = $purchaseData['batch_no'];
    //                 $store->branch_id = auth()->user()->branch_id;
    //                 $store->capacity_qty_available = 0;
    //             }

    //             // Update the store capacity with the adjusted quantity
    //             $store->capacity_qty_available += $purchaseData['capacity_qty'];
    //             $store->save();

    //             $purchases[] = $purchase;
    //         }

    //         DB::commit();

    //         return response()->json(['data' => $purchases, 'message' => 'Purchase record was added successfully', 'state' => true], 201);
    //     } catch (\Exception $e) {
    //         Log::channel('insertion_errors')->error('Error creating or updating user: ' . $e->getMessage());
    //         DB::rollBack();
    //         return response()->json(['message' => 'Failed to create purchases', 'state' => false], 500);
    //     }
    // }




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
