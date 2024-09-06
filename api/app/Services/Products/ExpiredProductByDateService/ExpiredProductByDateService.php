<?php

namespace App\Services\Products\ExpiredProductByDateService;

use App\Services\Products\ProductTypeService\ProductTypeRepository;

class ExpiredProductByDateService
{
    protected $productTypeRepository;

    public function __construct(ProductTypeRepository $productTypeRepository)
    {
        $this->productTypeRepository = $productTypeRepository;
    }

    
    public function index($data = null, $id = null)
    {
        return $this->productTypeRepository->getexpiredProductByDate($data, $id);
    }
}
