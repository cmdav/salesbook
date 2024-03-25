<?php

namespace App\Http\Controllers\Product;
use App\Http\Controllers\Controller;
use App\Services\Products\ProductSubCategoryService\ProductSubCategoryService;
use Illuminate\Http\Request;

class SearchProductSubCategoryController extends Controller
{
      protected $productSubCategoryService;

    public function __construct(ProductSubCategoryService $productSubCategoryService)
    {
       $this->productSubCategoryService = $productSubCategoryService;
    }
   
    public function show($id)
    {
        $productSubCategory =$this->productSubCategoryService->searchProductSubCategory($id);
        return response()->json($productSubCategory);
    }

   
}
