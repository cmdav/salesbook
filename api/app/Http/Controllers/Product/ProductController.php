<?php

namespace App\Http\Controllers\Product;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProductFormRequest;
use App\Services\Products\ProductService\ProductService;
use App\Models\product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
     protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }
    public function index()
    {
        $product = $this->productService->getAllProduct();
        return response()->json($product);
    }

    public function store(ProductFormRequest $request)
    {
        $product = $this->productService->createProduct($request->all());
        return response()->json($product, 201);
    }

    public function show($id)
    {
        $product = $this->productService->getProductById($id);
        return response()->json($product);
    }

    public function update($id, Request $request)
    {
       
        $product = $this->productService->updateProduct($id, $request->all());
        return response()->json($product);
    }

    public function destroy($id)
    {
        $this->productService->deleteProduct($id);
        return response()->json(null, 204);
    }
}
