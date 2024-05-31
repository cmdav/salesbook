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
        return $this->productSubCategoryService->createProductSubCategory($request->all());
       
    }

    public function show($id)
    {
        $productSubCategory =$this->productSubCategoryService->getProductSubCategoryById($id);
        return response()->json($productSubCategory);
    }

    public function update($id, ProductSubcategoryFormRequest $request)
    {
       
        return $this->productSubCategoryService->updateProductSubCategory($id, $request->all());
       
    }

    public function destroy($id)
    {
        return $this->productSubCategoryService->deleteProductSubCategory($id);
       
    }
}
