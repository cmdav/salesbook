<?php

namespace App\Services\Supply\SupplyToCompanyService;

use App\Models\SupplyToCompany;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;

class SupplyToCompanyRepository 
{
   
    public function productSuppliedToCompany($supplierId)
    {
      
        $supplyToCompany =SupplyToCompany::select('supplier_id','organization_id','supplier_product_id')->
                        with('supplier_product:id,product_name,product_image,product_description')
                        ->where('supplier_id', $supplierId)
                        ->paginate(3);

                $supplyToCompany->getCollection()->transform(function($supplyToCompany){

                    return $this->transformProduct($supplyToCompany);
                });
       
        return $supplyToCompany;
    }
    private function transformProduct($supplyToCompany){

        return [
            'product_name'=>optional($supplyToCompany->supplier_product)->product_name,
            'product_image'=>optional($supplyToCompany->supplier_product)->product_image,
            'product_description'=>optional($supplyToCompany->supplier_product)->product_description,
        ];

    }
    public function create(array $data)
    {
       
        return SupplyToCompany::create($data);
    }

    public function findById($id)
    {
        return SupplyToCompany::find($id);
    }

    public function update($id, array $data)
    {
        $SupplyToCompany = $this->findById($id);
      
        if ($SupplyToCompany) {

            $SupplyToCompany->update($data);
        }
        return $SupplyToCompany;
    }

    public function delete($id)
    {
        $SupplyToCompany = $this->findById($id);
        if ($SupplyToCompany) {
            return $SupplyToCompany->delete();
        }
        return null;
    }
}
