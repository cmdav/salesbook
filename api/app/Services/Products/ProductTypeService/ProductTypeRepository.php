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
    public function index()
    {
    

            $productType = ProductType::with('product:id,product_name','suppliers:id,first_name,last_name,phone_number')
                    ->latest()->paginate(20);

                    $productType->getCollection()->transform(function ($productType) {
                        return $this->transformProductType($productType);
                    });
            
                    return $productType;

    }

    public function getProductTypeByProductId($id)
    {
       
        $ProductType = ProductType::select('id','product_type','product_type_image','supplier_id')
                                    ->with('suppliers')
                                    ->where('product_id', $id)
                                    ->paginate(20);
        

        return $ProductType;

        //return ProductType::latest()->paginate(3);

    }
    private function transformProductType($ProductType){

        return [
            'id'=>$ProductType->id,
            'product_type'=>$ProductType->product_type,
            'product_type_description'=>$ProductType->product_type_description,
            'product_type_image'=>$ProductType->product_type_image,
            'supplier_name'=>optional($ProductType->suppliers)->first_name."(".optional($ProductType->suppliers)->last_name .")",
            'supplier_phone_number'=>optional($ProductType->suppliers)->phone_number,
            'product_name'=>optional($ProductType->product)->product_name,
            
           
         
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
