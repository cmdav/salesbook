<?php

namespace App\Services\Inventory\PurchaseService;

use App\Models\Purchase;
use App\Models\SupplierRequest;
use App\Models\Inventory;
use App\Models\Sale;
use App\Models\Price;
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
        return [
            'id' => $purchase->id,
            //'product_type_id' => $purchase->product_type_id,
            //'supplier_id' => $purchase->supplier_id,
            // 'price_id' => $purchase->price_id,

            'product_type_id' => optional($purchase->productType)->product_type_name,
            'product_type_image' => optional($purchase->productType)->product_type_image,
            'product_type_description' => optional($purchase->productType)->product_type_description,
            'batch_no' => $purchase->batch_no,
            'quantity' => $purchase->quantity,

            //'product_identifier' => $purchase->product_identifier,
            'expiry_date' => $purchase->expiry_date,
            //'status' => '',
            // 'created_by' => $purchase->created_by,
            // 'updated_by' => $purchase->updated_by,
            // 'created_at' => $purchase->created_at,
            // 'updated_at' => $purchase->updated_at,
            'price' => $purchase->price ? $purchase->price->cost_price : 0,

            'created_by' => optional($purchase->creator)->fullname,
            'updated_by' => optional($purchase->updater)->fullname,
            //'system_price' => optional($purchase->price)->system_price,
            //'price_discount' => optional($purchase->price)->discount, 
            //'price_status' => optional($purchase->price)->status,
            //'organization_id' => optional($purchase->price)->organization_id,
            //'currency_name' => optional($purchase->currency)->currency_name,
            //'currency_symbol' => optional($purchase->currency)->currency_symbol,
          
        ];
    }
    public function create(array $data)
    {
    DB::beginTransaction();
    
    try {
        $purchases = [];
        
        foreach ($data['purchases'] as $purchaseData) {
           
            if (isset($purchaseData['price_id']) && is_numeric($purchaseData['price_id'])) {
                   
                $price = new Price();
                $price->product_type_id = $purchaseData['product_type_id'];
                $price->supplier_id = $purchaseData['supplier_id'];
                $price->cost_price = $purchaseData['cost_price'];
                $price->selling_price = $purchaseData['selling_price'];
                $price->status = 1;
                $price->save();

                $purchaseData['price_id'] = $price->id;
            }

          
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
        return 'Purchase created successfully!';
    } catch (\Exception $e) {
        DB::rollBack();
        return 'Failed to create purchases';
    }
}



    public function findById($id)
    {
        return Purchase::find($id);
    }

    public function update($id, array $data)
    {
        $Purchase = $this->findById($id);
      
        if ($Purchase) {

            $Purchase->update($data);
        }
        return $Purchase;
    }

    public function delete($id)
    {
        $Purchase = $this->findById($id);
        if ($Purchase) {
            return $Purchase->delete();
        }
        return null;
    }
}
