<?php

namespace App\Http\Controllers\Product;
use App\Http\Controllers\Controller;
use App\Services\Products\ProductCategoryService\ProductCategoryService;
use Illuminate\Http\Request;

class SearchProductCategoryController extends Controller
{
      protected $productCategoryService;

    public function __construct(ProductCategoryService $productCategoryService)
    {
        $this->productCategoryService = $productCategoryService;
    }
   

    public function show($id)
    {
        $productCategory = $this->productCategoryService->searchProductCategory($id);
        return response()->json($productCategory);
    }


}
