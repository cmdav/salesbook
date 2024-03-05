<?php

namespace App\Services\Supply\SupplierProductService;

use App\Models\SupplierProduct;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;

class SupplierProductRepository 
{
    public function index()
    {
       
        return SupplierProduct::latest()->paginate(20);

    }
    public function listAllSupplierProduct()
    {
       
        return SupplierProduct::select('id', 'product_name','product_description')->latest()->get();
    }
    public function productSuppliedToCompany()
    {
       
        return SupplierProduct::with('storeItem')->get();
       

    }
    public function create(array $data)
    {
       
        return SupplierProduct::create($data);
    }

    public function findById($id)
    {
        return SupplierProduct::find($id);
    }

    public function update($id, array $data)
    {
        $supplierProduct = $this->findById($id);
      
        if ($supplierProduct) {

            $supplierProduct->update($data);
        }
        return $supplierProduct;
    }

    public function delete($id)
    {
        $supplierProduct = $this->findById($id);
        if ($supplierProduct) {
            return $supplierProduct->delete();
        }
        return null;
    }
}
