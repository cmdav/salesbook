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
       
        $ProductType = ProductType::all();
        

        return $ProductType;

        //return ProductType::latest()->paginate(3);

    }
    private function transformProduct($supplyToCompany){

        return [
            'product_name'=>optional($supplyToCompany->supplier_product)->product_name,
            'product_image'=>optional($supplyToCompany->supplier_product)->product_image,
            'product_description'=>optional($supplyToCompany->supplier_product)->product_description,
            'quantity_remaining'=>$supplyToCompany->quantity_remain,
            'pending_request'=>$supplyToCompany->pending_request,
            'completed_request'=>$supplyToCompany->completed_request,
            'total_sales'=>$supplyToCompany->total_sales,
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
