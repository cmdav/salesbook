<?php

namespace App\Services\Products\ProductCategoryService;

use App\Models\ProductCategory;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;

class ProductCategoryRepository 
{

    private function query(){
    {
       
        return ProductCategory::select('id','category_name','category_description')->latest();

    }
    
    }
    public function index()
    {
        
        $productCategories = ProductCategory::select('id','category_name','category_description', 'created_by','updated_by')->latest()->with('creator','updater')->get();
       
        $transformed = $productCategories->map(function($productCategory) {
            return [
                'id' => $productCategory->id,
                'category_name' => $productCategory->category_name,
                'category_description' => $productCategory->category_description,
                'created_by' => $productCategory->creator->fullname ?? '',  
                'updated_by' => $productCategory->updater->fullname ?? ''
            ];
        });

        return $transformed;
       

    }
    public function searchProductCategory($searchCriteria)
    {
       
        return $this->query()->where('category_name', 'like', '%' . $searchCriteria . '%')->latest()->get();;

    }
    
    public function create(array $data)
    {
       
        return ProductCategory::create($data);
    }

    public function findById($id)
    {
        return ProductCategory::find($id);
    }

    public function update($id, array $data)
    {
        $productCategory = $this->findById($id);
      
        if ($productCategory) {

            $productCategory->update($data);
        }
        return $productCategory;
    }

    public function delete($id)
    {
        $productCategory = $this->findById($id);
        if ($productCategory) {
            return $productCategory->delete();
        }
        return null;
    }
}
