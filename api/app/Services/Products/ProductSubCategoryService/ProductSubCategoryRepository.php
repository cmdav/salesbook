<?php

namespace App\Services\Products\ProductSubCategoryService;

use App\Models\ProductSubCategory;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;

class ProductSubCategoryRepository 
{

    private function query(){
        
        return ProductSubCategory::select('id','category_id', 'sub_category_name','sub_category_description')
                 ->with('category:id,category_name');
    }
    public function index()
    {
       
        $productSubCategory = $this->query()->latest()->paginate(20);
                            $productSubCategory->getCollection()->transform(function($productSubCategory){
                                return $this->transformProductService($productSubCategory);
                            });     
                        return $productSubCategory;                 

    }
    public function onlySubProductCategory($category_id)
    {
     
        return ProductSubCategory::select('id', 'sub_category_name')->where('category_id', $category_id)->get();               

    }
    public function searchProductSubCategory($searchCriteria)
    {
     
         $productSubCategory =$this->query()->where('sub_category_name', 'like', '%' . $searchCriteria . '%')->get();     
                    $productSubCategory->transform(function($productSubCategory){
                        return $this->transformProductService($productSubCategory);
                    });     
                return $productSubCategory;           

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
            'id' => $productSubCategory->id,
             'sub_category_id' => $productSubCategory->category_id,
            'sub_category_name' => $productSubCategory->sub_category_name,
            'sub_category_description' => $productSubCategory->sub_category_description,
            'category_id' => optional($productSubCategory->category)->category_name,
        ];
    }
}
