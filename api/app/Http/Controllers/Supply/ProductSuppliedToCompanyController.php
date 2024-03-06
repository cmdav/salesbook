<?php

namespace App\Http\Controllers\Supply;
use App\Http\Controllers\Controller;
use App\Services\Supply\SupplyToCompanyService\SupplyToCompanyService;



class ProductSuppliedToCompanyController extends Controller
{
    protected $supplyToCompanyService;
     

    public function __construct(SupplyToCompanyService $supplyToCompanyService)
    {
       
        $this->supplyToCompanyService = $supplyToCompanyService;
       
    }
    public function show($supplierId){
       
        return $this->supplyToCompanyService->productSuppliedToCompany($supplierId);

    }
   

   
}
