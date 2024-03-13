<?php

namespace App\Services\Products\ProductCategoryService;

use App\Models\ProductCategory;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;

class ProductCategoryRepository 
{
    public function index()
    {
       
        return ProductCategory::select('id','category_name','category_description')->latest()->get();

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
