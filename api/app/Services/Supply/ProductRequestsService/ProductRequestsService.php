<?php

namespace App\Services\Supply\ProductRequestsService;
use App\Services\Supply\ProductRequestsService\ProductRequestsRepository;


class ProductRequestsService 
{
    protected $productRequestsRepository;

    public function __construct(ProductRequestsRepository $productRequestsRepository)
    {
        $this->productRequestsRepository = $productRequestsRepository;
    }
  
    public function getAllProductRequest()
    {
      
        return $this->productRequestsRepository->getAllProductRequest();
    }
    public function createRequest($request){

        return $this->productRequestsRepository->createRequest($request);
    }
    
}
