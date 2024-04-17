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
            'expired_date' => $purchase->expired_date,
            //'status' => '',
            // 'created_by' => $purchase->created_by,
            // 'updated_by' => $purchase->updated_by,
            // 'created_at' => $purchase->created_at,
            // 'updated_at' => $purchase->updated_at,
            'price' => $purchase->price,
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
        
        foreach ($data['purchases'] as $purchaseData) {
            // Create a new Purchase object
            $purchase = new Purchase();
            $purchase->product_type_id = $purchaseData['product_type_id'];
            $purchase->supplier_id = $purchaseData['supplier_id'];
            $purchase->price = $purchaseData['price'];
            //$purchase->currency_id = $purchaseData['currency_id'];
            $purchase->batch_no = $purchaseData['batch_no'];
            $purchase->quantity = $purchaseData['quantity'];
            $purchase->product_identifier = $purchaseData['product_identifier'];
            $purchase->expired_date = $purchaseData['expired_date'];
            $purchase->save();
        }
        return response()->json(['message' => 'Permissions created successfully!'], 201);  
        // return Purchase::create($data);
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
