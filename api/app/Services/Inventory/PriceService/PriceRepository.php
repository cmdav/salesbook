<?php

namespace App\Services\Inventory\PriceService;

use App\Models\Price;
use App\Models\SupplierRequest;
use App\Models\Inventory;
use App\Models\Sale;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;

class PriceRepository 
{
    public function index()
    {
        $Price = $this->queryCommon()->paginate(2);

        return $this->transformAndReturn($Price);
    }
    public function getAllPriceByProductType($id)
    {
        return Price::select('id','cost_price')->where('product_type_id', $id)->get();

       
    }
    public function getLatestPriceByProductType($id)
        {
           
            $price = Price::select('id', 'selling_price')->where('product_type_id', $id)->where('status', 1)->first();

            // If there is no such price, then just get the latest price regardless of the status.
            if (is_null($price)) {
                $price = Price::select('id', 'selling_price')->where('product_type_id', $id)->latest('created_at') ->first();
            }

            return $price;
        }


    public function getPriceByProductType($id)
    {
        $Price = $this->queryCommon()->where('product_type_id', $id)->paginate(2);

        return $this->transformAndReturn($Price);
    }

    private function queryCommon()
    {
        return Price::with(
            'productType:id,product_type_name,product_type_image,product_type_description',
            'currency:id,currency_name,currency_symbol',
            // 'supplier:id,first_name,last_name,phone_number'
        )->orderBy('status', 'desc');
    }

    private function transformAndReturn($Price)
    {
        $Price->getCollection()->transform(function ($Price) {
            return $this->transformProduct($Price);
        });

        return $Price;
    }

    private function transformProduct($price){

        return [
            'id'=>$price->id,
            'product_type_id'=>optional($price->productType)->id,
            'product_type_image'=>optional($price->productType)->product_type_image,
            'product_type_name'=>optional($price->productType)->product_type_name,
            'product_type_description'=>optional($price->productType)->product_type_description,
           
            'cost_rice'=>$price->cost_price,
            'auto_generated_selling_price'=>$price->auto_generated_selling_price,
            'selling_price'=>$price->selling_price,
            'currency'=>optional($price->currency)->currency_name."(".optional($price->currency)->currency_symbol .")",
            'discount'=>$price->discount,
            'status'=>$price->status,
            // 'supplier_name'=>optional($price->supplier)->first_name."(".optional($price->supplier)->last_name .")",
            // 'supplier_phone_number'=>optional($price->supplier)->phone_number,
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
