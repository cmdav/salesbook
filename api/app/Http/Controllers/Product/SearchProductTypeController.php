<?php

namespace App\Http\Controllers\Product;
use App\Http\Controllers\Controller;
use App\Services\Products\ProductTypeService\ProductTypeService;
use Illuminate\Http\Request;


class SearchProductTypeController extends Controller
{
    
     protected $productTypeService;

    public function __construct( ProductTypeService $productTypeService)
    {
       
        $this->productTypeService = $productTypeService;
      
    }
    public function show($id)
    {
        $product = $this->productTypeService->searchProductType($id);
        return response()->json($product);
    }


}
