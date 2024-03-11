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
    

        return ProductType::latest()->paginate(20);

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
