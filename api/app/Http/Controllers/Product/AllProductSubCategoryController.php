<?php

namespace App\Http\Controllers\Product;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProductSubcategoryFormRequest;
use App\Services\Products\ProductSubCategoryService\ProductSubCategoryService;
use App\Models\ProductSubCategory;


class AllProductSubCategoryController extends Controller
{
      protected $productSubCategoryService;

    public function __invoke(ProductSubCategoryService $productSubCategoryService)
    {
       $this->productSubCategoryService = $productSubCategoryService;
       return $this->productSubCategoryService->onlySubProductCategory();
    }
   
   
}
