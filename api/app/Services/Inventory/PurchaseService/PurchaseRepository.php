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
    private function query(){

        return  Purchase::with('suppliers','currency','productType')->latest();
           
    
    }
    public function index()
    {
       $Purchase = $this->query()->paginate(20);

        $Purchase->getCollection()->transform(function($Purchase){

            return $this->transformProduct($Purchase);
        });
        return $Purchase;

        
    }
    public function searchPurchase($searchCriteria)
    {
       $Purchase = $this->query()
       ->where(function($query) use ($searchCriteria) {
            $query->whereHas('productType', function($q) use ($searchCriteria) {
                $q->where('product_type_name', 'like', '%' . $searchCriteria . '%');
            });
        })->get();

        $Purchase->transform(function($Purchase){

            return $this->transformProduct($Purchase);
        });
        return $Purchase;

        
    }
    private function transformProduct($purchase){
        // Assuming $purchase is the purchase data returned from the API
        $cost_price =  $purchase->price ? $purchase->price->cost_price : 0;
        $formatted_cost_price = number_format($cost_price, 2, '.', ',');

        $selling_price = $purchase->price ? $purchase->price->selling_price : 0;
        $formatted_selling_price = number_format($selling_price, 2, '.', ',');
        return [
            'id' => $purchase->id,
          
            'product_type_name' => optional($purchase->productType)->product_type_name,
            'product_type_image' => optional($purchase->productType)->product_type_image,
            'product_type_description' => optional($purchase->productType)->product_type_description,
            'batch_no' => $purchase->batch_no,
            'quantity' => $purchase->quantity,

            //'product_identifier' => $purchase->product_identifier,
            'expiry_date' => $purchase->expiry_date,
            'cost_price' => $formatted_cost_price,
            'selling_price' => $formatted_selling_price,
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
           
            //if (isset($purchaseData['price_id']) && is_numeric($purchaseData['price_id'])) {
                if (empty($purchaseData['price_id'])) {  

                    //return "price_id is empty";
                   
                $price = new Price();
                $price->product_type_id = $purchaseData['product_type_id'];
                $price->supplier_id = $purchaseData['supplier_id'];
                $price->cost_price = $purchaseData['cost_price'];
                $price->selling_price = $purchaseData['selling_price'];
                $price->batch_no = $purchaseData['batch_no'];
                $price->status = 1;
                $price->save();

                $purchaseData['price_id'] = $price->id;
            }
            // return "price_id is not empty";
          
            if (!empty($purchaseData['supplier_id'])) {
                $existingRecord = \App\Models\SupplierProduct::where('product_type_id', $purchaseData['product_type_id'])
                                                            ->where('supplier_id', $purchaseData['supplier_id'])
                                                            ->where('batch_no', $purchaseData['batch_no'])
                                                            ->first();
                if (!$existingRecord) {
                    $supplierProduct = new \App\Models\SupplierProduct();
                    $supplierProduct->product_type_id = $purchaseData['product_type_id'];
                    $supplierProduct->supplier_id = $purchaseData['supplier_id'];
                    $supplierProduct->batch_no = $purchaseData['batch_no'];
                    $supplierProduct->save();
                }
            }

            $purchase = new Purchase();
            $purchase->product_type_id = $purchaseData['product_type_id'];
            $purchase->supplier_id = $purchaseData['supplier_id'];
            $purchase->price_id = $purchaseData['price_id'];
            $purchase->batch_no = $purchaseData['batch_no'];
            $purchase->quantity = $purchaseData['quantity'];
            $purchase->product_identifier = $purchaseData['product_identifier'];
            $purchase->expiry_date = $purchaseData['expiry_date'];
            $purchase->save();
            
            $purchases[] = $purchase;
        }

        DB::commit();
        return response()->json(['data' =>$purchase] , 201);
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json(['message' =>'Failed to create purchases'] , 500);
        //return 'Failed to create purchases';
    }
}



    public function findById($id)
    {
        return Purchase::find($id);
    }

      public function update($id, array $data)
    {
        $purchase = Purchase::find($id);

        if ($purchase) {
            $originalQuantity = $purchase->quantity;
            $newQuantity = $data['quantity'];
            $quantityDifference = $newQuantity - $originalQuantity;

            $purchase->update($data);

            // Update store quantity
            $store = Store::where('product_type_id', $purchase->product_type_id)
                          ->where('batch_no', $purchase->batch_no)
                          ->first();

            if ($store) {
                $store->quantity_available += $quantityDifference;
                if ($store->quantity_available < 0) {
                    $store->quantity_available = 0;
                }
                $store->save();
            }
        }

        return $purchase;
    }

    public function delete($id)
    {
        $purchase = Purchase::find($id);

        if ($purchase) {
          
            $store = Store::where('product_type_id', $purchase->product_type_id)
                          ->where('batch_no', $purchase->batch_no)
                          ->first();

            if ($store) {
                $store->quantity_available -= $purchase->quantity;
                if ($store->quantity_available < 0) {
                    $store->quantity_available = 0;
                }
                $store->save();
            }

            return $purchase->delete();
        }

        return null;
    }
    
}
