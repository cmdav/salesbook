<?php

namespace App\Services\Products\ProductService;

use App\Models\Product;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use Exception;

class ProductRepository 
{
    public function index()
    {
       // return \App\Models\Product::with('productType','measurement','product_category','subCategory','suppliers')->paginate(20);
    
       
        $product =Product::latest()->with('subCategory.category')
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
      try{
       $product = Product::findorFail($id);
      
        if ($product) {

          $product  =$product->update($data);
           return response()->json([
            'success' =>true,
            'message' => 'Update successful',
            'data' => $product
        ], 200);
        }
    } catch (Exception $e) {
        Log::channel('insertion_errors')->error('Error creating or updating user: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'This Product could not be updated',
        ], 500);
    }
    }

    public function delete($id)
    {
        try{
       $product = $this->findById($id);
        if ($Product) {
            $product->delete();
            return response()->json([
                'success' => true,
                'message' => 'Deletion successful',
            ], 200);
        }
    } catch (Exception $e) {
        Log::channel('insertion_errors')->error('Error creating or updating user: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'This Product is already in use',
        ], 500);
    }
    }

    public function transformProduct($product){

        return [
            "id" => $product->id,
        //     "product_image" => $product->product_image,
        //     "product_name" => $product->product_name,
        //     "product_description" => $product->product_description,
        //     'view_price' => 'view price',
        //    // "unit" => optional($product->measurement)->unit,
        //     "cat_id" => optional($product->subCategory)->category_id,
        //     "category_id" => optional($product->subCategory)->category ? optional($product->subCategory->category)->category_name : null,
        //     "measurement_id" => optional($product->measurement)->measurement_name,
        //     "product_sub_category_id" => optional($product->subCategory)->sub_category_name,
        //     "purchase_price" => "",
        //     "selling_price" => "",
        //     "supplier_fullname" => "",
        //     "supplier_number" => "",
        //     "quantity" => "",
        //     "date_created" => "",
          
        ];
        
    }
}
