<?php

namespace App\Services\Products\ProductTypeService;

use App\Models\ProductType;
use App\Models\SupplierRequest;
use App\Models\Inventory;
use App\Models\Sale;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;

class ProductTypeRepository 
{
    private function query(){

        return ProductType::with([
            'product:id,category_id,product_name', 
            'product.product_category:id,category_name',
            'store:id,product_type_id,quantity_available',
            'suppliers:id,first_name,last_name,phone_number',
            'activePrice' => function ($query) {
                $query->select('id', 'product_type_id', 'cost_price', 'selling_price', 'discount');
            }
        ])->latest();
    }
    public function index()
    {
        return $this->getProductTypes();
    }
    public function searchProductType($searchCriteria){

        $query = $this->query()
            ->where('product_type_name', 'like', '%' . $searchCriteria . '%')
            ->Orwhere(function($query) use ($searchCriteria) {
                $query->whereHas('product', function($q) use ($searchCriteria) {
                    $q->where('product_name', 'like', '%' . $searchCriteria . '%');
                });
            // ->orWhereHas('product.product_category', function($q) use ($searchCriteria) {
            //     $q->where('category_name', 'like', '%' . $searchCriteria . '%');
            // });
        });
    
        $productTypes = $query->get();
    
        $productTypes->transform(function ($productType) {
            return $this->transformProductType($productType);
        });

         return $productTypes;
    }
    
    public function getProductTypeByProductId($id)
    {
        return $this->getProductTypes($id);
    }
    
    public function getProductTypeByName()
    {
        return ProductType::select('id','product_type_name')->get();
    }
    private function getProductTypes($productId = null)
    {
                    $query =$this->query();
                    if ($productId) {
                        $query->where('product_id', $productId);
                    };
                
                    $productTypes = $query->paginate(2);
                
                    $productTypes->getCollection()->transform(function ($productType) {
                        return $this->transformProductType($productType);
                    });
    
                     return $productTypes;
    }
    
    private function transformProductType($productType){

        $activePrice = $productType->activePrice;
        
        return [
            'id' => $productType->id,
            'product_name' => optional($productType->product)->product_name,
            'product_type_name' => $productType->product_type_name,
            'product_type_image' => $productType->product_type_image,
            'product_image' => $productType->product_type_image,

            // 'product_type_name' =>$productType->product_type_name,
            'view_price' => 'view price',
            'product_description' => $productType->product_type_description,
            'product_type_description' => $productType->product_type_description,
            'product' => optional($productType->product)->product_name,
            'product_category' => optional($productType->product->product_category)->category_name,
            'product_sub_category' => optional($productType->product->product_category)->category_name,
            'quantity' => optional($productType->store)->quantity_available,

            ///////////added Product column
            "cat_id" => optional($productType->product->subCategory)->category_id,
            "category_id" => optional($productType->product->subCategory)->category ? optional($productType->product->subCategory->category)->category_name : null,
            "measurement_id" => optional($productType->product->measurement)->measurement_name,
            "product_sub_category_id" => optional($productType->product->subCategory)->sub_category_name,

            //'status' => optional($productType->store)->quantity_available > 0 ? 'Available' : 'Not Available',
            'purchasing_price' => optional($productType->latestPurchase)->price,
            'selling_price' => optional($activePrice)->selling_price,
            'supplier_name' => optional($productType->suppliers)->first_name . ' ' . optional($productType->suppliers)->last_name,
            'supplier_phone_number' => optional($productType->suppliers)->phone_number,
            'date_created' =>$productType->created_at
        ];
            

    }
    
    public function create(array $data)
    {
    
        return ProductType::create($data);
    }

    public function findById($id)
    {
        return ProductType::find($id);
    }

    public function update($id, array $data)
    {
        $ProductType = $this->findById($id);
      
        if ($ProductType) {

            $ProductType->update($data);
        }
        return $ProductType;
    }

    public function delete($id)
    {
        $ProductType = $this->findById($id);
        
        if ($ProductType) {
            return $ProductType->delete();
        }
        return null;
    }
}
