<?php

namespace App\Http\Controllers\Supply;
use App\Http\Controllers\Controller;
use App\Services\Supply\SupplyToCompanyService\SupplyToCompanyService;



class ProductSuppliedToCompanyController extends Controller
{
    protected $supplyToCompanyService;
     

    public function __invoke(SupplyToCompanyService $supplyToCompanyService)
    {
       
        $this->supplyToCompanyService = $supplyToCompanyService;
        return $this->supplyToCompanyService->productSuppliedToCompany();
    }
   

   
}
