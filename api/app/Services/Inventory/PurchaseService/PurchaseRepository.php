<?php

namespace App\Services\Inventory\PurchaseService;

use App\Models\Purchase;
use App\Models\SupplierRequest;
use App\Models\Inventory;
use App\Models\Sale;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;

class PurchaseRepository 
{
    public function index()
    {
       $Purchase =Purchase::with('price','suppliers','currency','productType')->paginate(20);
       

        $Purchase->getCollection()->transform(function($Purchase){

            return $this->transformProduct($Purchase);
        });


    return $Purchase;

        
    }
    private function transformProduct($purchase){
        // Assuming $purchase is the purchase data returned from the API
        return [
            'id' => $purchase->id,
            'product_type_id' => $purchase->product_type_id,
            'supplier_id' => $purchase->supplier_id,
            'price_id' => $purchase->price_id,
            'currency_id' => $purchase->currency_id,
            'discount' => $purchase->discount,
            'batch_no' => $purchase->batch_no,
            'quantity' => $purchase->quantity,
            'product_identifier' => $purchase->product_identifier,
            'expired_date' => $purchase->expired_date,
            'purchase_by' => $purchase->purchase_by,
            'status' => $purchase->status,
            // 'created_by' => $purchase->created_by,
            // 'updated_by' => $purchase->updated_by,
            // 'created_at' => $purchase->created_at,
            // 'updated_at' => $purchase->updated_at,
            'product_type_price' => optional($purchase->price)->product_type_price,
            'system_price' => optional($purchase->price)->system_price,
            'price_discount' => optional($purchase->price)->discount, 
            'price_status' => optional($purchase->price)->status,
            'organization_id' => optional($purchase->price)->organization_id,
            'currency_name' => optional($purchase->currency)->currency_name,
            'currency_symbol' => optional($purchase->currency)->currency_symbol,
            'product_type' => optional($purchase->productType)->product_type,
            'product_type_image' => optional($purchase->productType)->product_type_image,
            'product_type_description' => optional($purchase->productType)->product_type_description,
        ];
    }
    







    
    public function create(array $data)
    {
       
        return Purchase::create($data);
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
