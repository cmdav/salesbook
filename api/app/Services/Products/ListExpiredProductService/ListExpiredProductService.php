<?php

namespace App\Services\Products\ListExpiredProductService;

use App\Services\Products\ProductTypeService\ProductTypeRepository;

class ListExpiredProductService
{
    protected $productTypeRepository;

    public function __construct(ProductTypeRepository $productTypeRepository)
    {
        $this->productTypeRepository = $productTypeRepository;
    }

    
    public function index($data = null, $id = null)
    {
        return $this->productTypeRepository->getlistExpiredProduct($data, $id);
    }
}
