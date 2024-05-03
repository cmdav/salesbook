<?php

namespace App\Services\Inventory\PriceService;

use App\Models\PriceNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;

class PriceNotificationRepository 
{
    public function index()
    {
        $priceNotification= PriceNotification::select('id','product_type_id','supplier_id','cost_price','selling_price','status')
                                  ->with('productTypes:id,product_type_name,product_type_image',
                                         'supplier:id,first_name,last_name,contact_person,phone_number')->paginate(20);

                                         $priceNotification->getCollection()->transform(function ($Price) {
                                            return $this->transformProduct($Price);
                                        });
                                

                                         return  $priceNotification;
    }

    private function transformProduct($price){

        return [
            'id' => $price->id,
            'product_type_name' => optional($price->productTypes)->product_type_name,
            'product_type_image' => optional($price->productTypes)->product_type_image,
            'product_type_description' => optional($price->productTypes)->product_type_description,
            'cost_price' => $price->cost_price,
            'selling_price' => $price->selling_price,
            'status' => $price->status,
            'supplier_detail' =>  optional($price->supplier)->first_name." ".optional($price->supplier)->last_name." ".  optional($price->supplier)->contact_person,
            'supplier_phone_number' => optional($price->supplier)->phone_number
          
        ];

    }

    public function create(array $data)
    {
        DB::beginTransaction(); 
    
        try {
            $price = Price::create($data); 

            if ($data['status'] == 1) {
                Price::where('product_type_id', $data['product_type_id'])
                     ->where('id', '!=', $price->id) 
                     ->update(['status' => 0]);
            }
    
            DB::commit(); 
    
            return $price; 
        } catch (\Exception $e) {
            DB::rollBack(); 
            throw $e; 
        }
    }
    

    public function findById($id)
    {
        return Price::find($id);
    }

    public function update($id, array $data)
    {
        $Price = $this->findById($id);
      
        if ($Price) {

            $Price->update($data);
        }
        return $Price;
    }

    public function delete($id)
    {
        $Price = $this->findById($id);
        if ($Price) {
            
            return $Price->delete();
        }
        return null;
    }
}
