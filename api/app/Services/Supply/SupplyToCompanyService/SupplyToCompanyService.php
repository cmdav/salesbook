<?php

namespace App\Services\Supply\SupplyToCompanyService;
use App\Services\Supply\SupplyToCompanyService\SupplyToCompanyRepository;


class SupplyToCompanyService 
{
    protected $supplyToCompanyRepository;

    public function __construct(SupplyToCompanyRepository $supplyToCompanyRepository)
    {
        $this->supplyToCompanyRepository = $supplyToCompanyRepository;
    }
  
    public function productSuppliedToCompany()
    {
      
        return $this->supplyToCompanyRepository->productSuppliedToCompany();
    }
   
}
