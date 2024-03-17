<?php

namespace App\Services\Products\ProductService;

use App\Models\Product;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;

class ProductRepository 
{
    public function index()
    {
       
        $product =Product::latest()->with('measurement:id,measurement_name,unit', 'subCategory.category')
                                    ->withCount('productType')
                                    ->paginate(20);
       
       
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
         $product =Product::with('measurement:id,measurement_name,unit')->find($id);
         return $this->transformProduct($product);
    }

    public function update($id, array $data)
    {
       $product = Product::findorFail($id);
      
        if ($product) {

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
            "id" => $product->id,
            "product_image" => $product->product_image,
            "product_name" => $product->product_name,
            "product_description" => $product->product_description,
            "product_type" => $product->product_type_count,
           // "unit" => optional($product->measurement)->unit,
            "cat_id" => optional($product->subCategory)->category_id,
            "category_id" => optional($product->subCategory)->category ? optional($product->subCategory->category)->category_name : null,
            "measurement_id" => optional($product->measurement)->measurement_name,
            "product_sub_category_id" => optional($product->subCategory)->sub_category_name,
          
        ];
        
    }
}
