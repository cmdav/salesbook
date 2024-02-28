<?php

namespace App\Services\Products\ProductService;

use App\Models\Product;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;

class ProductRepository 
{
    public function index()
    {
       
        $product =Product::latest()->with('measurement:id,measurement_name,unit', 'subCategory.category')->paginate(20);
        //return $product;

        $product->getCollection()->transform(function($product){

            return $this->transformProduct($product);
        });
        return $product;

    }
    public function listAllProduct()
    {
       
        return Product::select('id','product_name')->get();
       

    }
    public function create(array $data)
    {
       
        return Product::create($data);
    }

    public function findById($id)
    {
         $product =Product::with('measurement:id,measurement_name,unit,sub_category')->find($id);
         return $this->transformProduct($product);
    }

    public function update($id, array $data)
    {
       $product = $this->findById($id);
      
        if ($Product) {

           $product->update($data);
        }
        return$product;
    }

    public function delete($id)
    {
       $product = $this->findById($id);
        if ($Product) {
            return$product->delete();
        }
        return null;
    }

    public function transformProduct($product){

        return [

            "id"=> $product->id,
            "product_name"=>$product->product_name,
            "product_description"=> $product->product_description,
            "product_image"=> $product->product_image,
            "measurement_name" => optional($product->measurement)->measurement_name,
            "unit" => optional($product->measurement)->unit,
            "product_category" =>   optional($product->subCategory)->category->category_name,
            "product_sub_category"=> optional($product->subCategory)->sub_category_name,
           

        ];
    }
}
