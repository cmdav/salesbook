<?php

namespace App\Http\Controllers\Product;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProductFormRequest;
use App\Services\Products\ProductService\ProductService;
use App\Models\product;
use Illuminate\Http\Request;
use App\Services\FileUploadService;

class AllProductController extends Controller
{
     protected $productService;
     

    public function __invoke(ProductService $productService)
    {
        $this->productService = $productService;
        return $this->productService->listAllProduct();
    }
   

   
}
