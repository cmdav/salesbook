<?php

namespace App\Services\Supply\ProductRequestsService;

use App\Models\SupplierRequest;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;

class ProductRequestsRepository 
{
   
    public function getAllProductRequest()
    {
      return 're';
    //    $productRequests =ProductRequests::select('supplier_id','organization_id','supplier_product_id')->
    //                     with('supplier_product:id,product_name,product_image,product_description')
    //                     ->where('supplier_id', $supplierId)
    //                     ->paginate(3);

    //            $productRequests->getCollection()->transform(function($ProductRequests){

    //                 return $this->transformProduct($ProductRequests);
    //             });
       
    //     return$productRequests;
    }
    private function transformProduct($ProductRequests){

        return [
            'product_name'=>optional($ProductRequests->supplier_product)->product_name,
            'product_image'=>optional($ProductRequests->supplier_product)->product_image,
            'product_description'=>optional($ProductRequests->supplier_product)->product_description,
        ];

    }
    public function createRequest(array $data)
    {
       
        return SupplierRequest::create($data);
    }

    public function findById($id)
    {
        return SupplierRequests::find($id);
    }

    public function update($id, array $data)
    {
       $SupplierRequests = $this->findById($id);
      
        if ($SupplierRequests) {

           $SupplierRequests->update($data);
        }
        return$SupplierRequests;
    }

    public function delete($id)
    {
       $SupplierRequests = $this->findById($id);
        if ($SupplierRequests) {
            return $SupplierRequests->delete();
        }
        return null;
    }
}
