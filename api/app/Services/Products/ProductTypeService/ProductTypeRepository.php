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
            'product:id,category_id,product_name,measurement_id,sub_category_id', 
            'product.measurement:id,measurement_name',
            'product.subCategory:id,sub_category_name',
            'store:id,product_type_id,quantity_available',
            'suppliers:id,first_name,last_name,phone_number',
            'activePrice' => function ($query) {
                $query->select('id',  'cost_price', 'selling_price','product_type_id');
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
                
                    $productTypes = $query->paginate(20);
                   
    
                    
                    $productTypes->getCollection()->transform(function ($productType) {
                        return $this->transformProductType($productType);
                    });
    
                     return $productTypes;
    }
    
    private function transformProductType($productType){

        return [
            'id' => $productType->id,
            'product_id' => optional($productType->product)->product_name,
            'product_ids' => optional($productType->product)->id,
            'product_type_name' => $productType->product_type_name,
            'product_type_image' => $productType->product_type_image,
            'product_type_description' => $productType->product_type_description,
            'view_price' => 'view price',
            'product_name' => optional($productType->product)->product_name,
            'product_description' => $productType->product_type_description,
            'product_image' => $productType->product_type_image,
            'product_category' => optional(optional($productType->product)->product_category)->category_name,
            'category_ids' => optional(optional($productType->product)->product_category)->id,
            'category_id' => optional(optional($productType->product)->product_category)->category_name,
    
            'product_sub_category' => optional(optional($productType->product)->subCategory)->sub_category_name,
            'sub_category_id' => optional(optional($productType->product)->subCategory)->id,
            'quantity_available' => optional($productType->store)->quantity_available ?? 0,
            "measurement_id" => optional(optional($productType->product)->measurement)->measurement_name,
    
            'purchasing_price' => optional($productType->latestPurchase)->price ?? 'Not set',
            'selling_price' => optional($productType->activePrice)->selling_price ?? 'Not set',
            'supplier_name' => trim((optional($productType->suppliers)->first_name ?? '') . ' ' . (optional($productType->suppliers)->last_name ?? '')) ?: 'None',
            'supplier_phone_number' => optional($productType->suppliers)->phone_number ?? 'None',
            'date_created' => $productType->created_at,
            'created_by' => optional($productType->creator)->fullname,
            'updated_by' => optional($productType->updater)->fullname,
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
            if($ProductType->type == 1){
                 $product= \App\Models\Product::find($ProductType->product_id);
                 $product->delete();
            }
            return $ProductType->delete();
        }
        return null;
    }
}
