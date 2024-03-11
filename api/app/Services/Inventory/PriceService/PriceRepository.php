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
        $Price = $this->queryCommon()->paginate(20);

        return $this->transformAndReturn($Price);
    }

    public function getPriceByProductType($id)
    {
        $Price = $this->queryCommon()->where('product_type_id', $id)->paginate(20);

        return $this->transformAndReturn($Price);
    }

    private function queryCommon()
    {
        return Price::with(
            'productType:id,product_type,product_type_image,product_type_description',
            'currency:id,currency_name,currency_symbol',
            'supplier:id,first_name,last_name,phone_number'
        );
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
            'product_type'=>optional($price->productType)->product_type,
            'product_type_description'=>optional($price->productType)->product_type_description,
            'product_type_image'=>optional($price->productType)->product_type_image,
            'product_type_price'=>$price->product_type_price,
            'system_price'=>$price->system_price,
            'currency'=>optional($price->currency)->currency_name."(".optional($price->currency)->currency_symbol .")",
            'discount'=>$price->discount,
            'status'=>$price->status,
            'supplier_name'=>optional($price->supplier)->first_name."(".optional($price->supplier)->last_name .")",
            'supplier_phone_number'=>optional($price->supplier)->phone_number,
        ];

    }

    public function create(array $data)
    {
       
        return Price::create($data);
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
