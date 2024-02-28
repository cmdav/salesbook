<?php

namespace App\Http\Controllers\Product;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProductSubcategoryFormRequest;
use App\Services\Products\ProductSubCategoryService\ProductSubCategoryService;
use App\Models\ProductSubCategory;
use Illuminate\Http\Request;

class ProductSubCategoryController extends Controller
{
      protected $productSubCategoryService;

    public function __construct(ProductSubCategoryService $productSubCategoryService)
    {
       $this->productSubCategoryService = $productSubCategoryService;
    }
    public function index()
    {
        $productSubCategory =$this->productSubCategoryService->getAllProductSubCategory();
        return response()->json($productSubCategory);
    }

    public function store(ProductSubcategoryFormRequest $request)
    {
        $productSubCategory =$this->productSubCategoryService->createProductSubCategory($request->all());
        return response()->json($productSubCategory, 201);
    }

    public function show($id)
    {
        $productSubCategory =$this->productSubCategoryService->getProductSubCategoryById($id);
        return response()->json($productSubCategory);
    }

    public function update($id, Request $request)
    {
       
        $productSubCategory =$this->productSubCategoryService->updateProductSubCategory($id, $request->all());
        return response()->json($productSubCategory);
    }

    public function destroy($id)
    {
       $this->productSubCategoryService->deleteProductSubCategory($id);
        return response()->json(null, 204);
    }
}
