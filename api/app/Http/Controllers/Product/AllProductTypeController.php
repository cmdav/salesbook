<?php

namespace App\Http\Controllers\Product;
use App\Http\Controllers\Controller;
use App\Services\Products\ProductTypeService\ProductTypeService;



class AllProductTypeController extends Controller
{
      protected $ProductTypeService;

    public function __invoke(ProductTypeService $ProductTypeService)
    {
       $this->ProductTypeService = $ProductTypeService;
      
       return $this->ProductTypeService->onlyProductTypeName();
    }
   
   
}
