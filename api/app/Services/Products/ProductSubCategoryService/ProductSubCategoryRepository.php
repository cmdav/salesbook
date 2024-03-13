<?php

namespace App\Services\Products\ProductSubCategoryService;

use App\Models\ProductSubCategory;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;

class ProductSubCategoryRepository 
{
    public function index()
    {
       
        $productSubCategory = ProductSubCategory::select('id','category_id', 'sub_category_name','sub_category_description')
                                    ->with('category:id,category_name')->latest()->paginate(3);
                            $productSubCategory->getCollection()->transform(function($productSubCategory){
                                return $this->transformProductService($productSubCategory);
                            });     
                        return $productSubCategory;                 

    }
    public function onlySubProductCategory($category_id)
    {
     
        return ProductSubCategory::select('id', 'sub_category_name')->where('category_id', $category_id)->get();               

    }
    public function create(array $data)
    {
       
        return ProductSubCategory::create($data);
    }

    public function findById($id)
    {
        return ProductSubCategory::find($id);
    }

    public function update($id, array $data)
    {
        $productSubCategory = $this->findById($id);
      
        if ($productSubCategory) {

            $productSubCategory->update($data);
        }
        return $productSubCategory;
    }

    public function delete($id)
    {
        $productSubCategory = $this->findById($id);
        if ($productSubCategory) {
            return $productSubCategory->delete();
        }
        return null;
    }
    private function transformProductService($productSubCategory){

        return [
             'sub_category_id' => $productSubCategory->id,
            'sub_category_name' => $productSubCategory->sub_category_name,
            'category_name' => optional($productSubCategory->category)->category_name,
        ];
    }
}
