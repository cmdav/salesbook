<?php

namespace App\Http\Controllers\Product;
use App\Http\Controllers\Controller;
use App\Services\Products\ProductTypeService\ProductTypeService;



class ProductTypeByIdController extends Controller
{
      protected $ProductTypeService;

    public function __invoke(ProductTypeService $ProductTypeService, $category_id)
    {
       $this->ProductTypeService = $ProductTypeService;
      
       return $this->ProductTypeService->getProductTypeByProductId($category_id);
    }
   
   
}
