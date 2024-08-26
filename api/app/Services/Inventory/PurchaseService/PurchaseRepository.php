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
            'productType:id,product_type_name,product_type_image,product_type_description,selling_unit_capacity_id',
            'productType.sellingUnitCapacity:id,selling_unit_id,selling_unit_capacity',
            'productType.sellingUnit' => function ($q) {
                $q->select('selling_units.id', 'selling_units.purchase_unit_id', 'selling_units.selling_unit_name');
            },
            'productType.sellingUnit.purchaseUnit' => function ($q) {
                $q->select('purchase_units.id', 'purchase_units.purchase_unit_name');
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
        } elseif(auth()->user()->role->role_name != 'Admin') {
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
        } elseif(auth()->user()->role->role_name != 'Admin') {
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
           // 'container_type_name' => $containerTypeName,
           // 'container_type_capacity' => optional($purchase->productType->containerCapacities)->container_capacity,
            //'container_qty' => $purchase->container_qty,
            'selling_unit_capacity' => optional($purchase->productType->sellingUnitCapacity)->selling_unit_capacity,
            'selling_unit_name' => optional($purchase->productType->sellingUnit)->selling_unit_name,
            'purchase_unit_name' => optional($purchase->productType->sellingUnit->purchaseUnit)->purchase_unit_name,

            'capacity_qty' => $purchase->capacity_qty,
            'branch_name' => optional($purchase->branches)->name,
            'batch_no' => $purchase->batch_no,
            'quantity' => $purchase->quantity,
            'expiry_date' => $purchase->expiry_date,
            'cost_price' => $formatted_cost_price,
            'selling_price' => $formatted_selling_price,
            'supplier' => optional($purchase->suppliers)->first_name . " " . optional($purchase->suppliers)->last_name,
            'created_by' => optional($purchase->creator)->fullname,
            'updated_by' => optional($purchase->updater)->fullname,
        ];
    }


    public function create(array $data)
    {
        DB::beginTransaction();

        try {
            $purchases = [];

            foreach ($data['purchases'] as $purchaseData) {
                // Empty for new price
                $price = new Price();
                $price->product_type_id = $purchaseData['product_type_id'];
                $price->supplier_id = $purchaseData['supplier_id'];
                $price->batch_no = $purchaseData['batch_no'];
                $price->status = 1;

                // purchaseData price id will be empty for initial price
                if (empty($purchaseData['price_id'])) {
                    $price->cost_price = $purchaseData['cost_price'];
                    $price->selling_price = $purchaseData['selling_price'];
                    $price->save();
                    $purchaseData['price_id'] = $price->id;
                } else {
                    $price->price_id = $purchaseData['price_id'];
                    $price->save();
                }

                if (!empty($purchaseData['supplier_id'])) {
                    // Get and save new supplier into the supplier product table
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

                $purchase = new Purchase();
                $purchase->product_type_id = $purchaseData['product_type_id'];
                $purchase->supplier_id = $purchaseData['supplier_id'];
                $purchase->price_id = $purchaseData['price_id'];
                $purchase->batch_no = $purchaseData['batch_no'];
                // $purchase->quantity = $purchaseData['quantity'];
                $purchase->product_identifier = $purchaseData['product_identifier'];
                $purchase->expiry_date = $purchaseData['expiry_date'];
                $purchase->capacity_qty = $purchaseData['capacity_qty'];
                // $purchase->container_qty = $purchaseData['container_qty'];

                $purchase->save();

                $productType = \App\Models\ProductType::find($purchaseData['product_type_id']);


                // Check if the store already exists
                $store = \App\Models\Store::where('product_type_id', $purchaseData['product_type_id'])
                                           ->where('batch_no', $purchaseData['batch_no'])
                                           ->first();

                if (!$store) {
                    $store = new \App\Models\Store();
                    $store->product_type_id = $purchaseData['product_type_id'];
                    $store->batch_no = $purchaseData['batch_no'];
                    $store->branch_id = auth()->user()->branch_id;
                }

                // Update the store capacity and container quantity

                $store->capacity_qty_available += $purchaseData['capacity_qty'];

                $store->save();

                $purchases[] = $purchase;
            }

            DB::commit();
            return response()->json(['data' => $purchases, 'message' => 'Purchase record was added successfully'], 201);
        } catch (\Exception $e) {
            Log::channel('insertion_errors')->error('Error creating or updating user: ' . $e->getMessage());
            DB::rollBack();
            return response()->json(['message' => 'Failed to create purchases'], 500);
        }
    }




    public function findById($id)
    {
        return Purchase::find($id);
    }

    //   public function update($id, array $data)
    // {
    //     $purchase = Purchase::find($id);

    //     if ($purchase) {
    //         $originalQuantity = $purchase->quantity;
    //         $newQuantity = $data['quantity'];
    //         $quantityDifference = $newQuantity - $originalQuantity;

    //         $purchase->update($data);

    //         // Update store quantity
    //         $store = Store::where('product_type_id', $purchase->product_type_id)
    //                       ->where('batch_no', $purchase->batch_no)
    //                       ->first();

    //         if ($store) {
    //             $store->quantity_available += $quantityDifference;
    //             if ($store->quantity_available < 0) {
    //                 $store->quantity_available = 0;
    //             }
    //             $store->save();
    //         }
    //     }

    //     return $purchase;
    // }

    // public function delete($id)
    // {
    //     $purchase = Purchase::find($id);

    //     if ($purchase) {

    //         $store = Store::where('product_type_id', $purchase->product_type_id)
    //                       ->where('batch_no', $purchase->batch_no)
    //                       ->first();

    //         if ($store) {
    //             $store->quantity_available -= $purchase->quantity;
    //             if ($store->quantity_available < 0) {
    //                 $store->quantity_available = 0;
    //             }
    //             $store->save();
    //         }

    //         return $purchase->delete();
    //     }

    //     return null;
    // }

}
