<?php

namespace App\Services\Products\ProductPriceListService;

use App\Services\Products\ProductTypeService\ProductTypeRepository;

class ProductPriceListService
{
    protected $productTypeRepository;

    public function __construct(ProductTypeRepository $productTypeRepository)
    {
        $this->productTypeRepository = $productTypeRepository;
    }

    
    public function index($data = null, $id = null)
    {
        return $this->productTypeRepository->getproductPriceList($data, $id);
    }
}
