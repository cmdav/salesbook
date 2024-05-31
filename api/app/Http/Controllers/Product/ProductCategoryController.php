<?php

namespace App\Http\Controllers\Product;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProductCategoryFormRequest;
use App\Services\Products\ProductCategoryService\ProductCategoryService;
use App\Models\ProductCategory;
use Illuminate\Http\Request;

class ProductCategoryController extends Controller
{
      protected $productCategoryService;

    public function __construct(ProductCategoryService $productCategoryService)
    {
        $this->productCategoryService = $productCategoryService;
    }
    public function index()
    {
        $productCategory = $this->productCategoryService->getAllProductCategory();
        return response()->json($productCategory);
    }

    public function store(ProductCategoryFormRequest $request)
    {
        return $this->productCategoryService->createProductCategory($request->all());
       
    }

    public function show($id)
    {
        $productCategory = $this->productCategoryService->getProductCategoryById($id);
        return response()->json($productCategory);
    }

    public function update($id, ProductCategoryFormRequest $request)
    {
       
        return $this->productCategoryService->updateProductCategory($id, $request->all());
       
    }

    public function destroy($id)
    {
        return $this->productCategoryService->deleteProductCategory($id);
     
    }
}
