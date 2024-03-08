<?php

namespace App\Services\Products\ProductSubCategoryService;
use App\Services\Products\ProductSubCategoryService\ProductSubCategoryRepository;


class ProductSubCategoryService 
{
    protected $productSubCategoryRepository;

    public function __construct(ProductSubCategoryRepository $productSubCategoryRepository)
    {
        $this->productSubCategoryRepository = $productSubCategoryRepository;
    }

    public function createProductSubCategory(array $data)
    {
       
        return $this->productSubCategoryRepository->create($data);
    }

    public function getAllProductSubCategory()
    {
       
        return $this->productSubCategoryRepository->index();
    }
    public function onlySubProductCategory($category_id){
        

        return $this->productSubCategoryRepository->onlySubProductCategory($category_id);
    }

    public function getProductSubCategoryById($id)
    {
        return $this->productSubCategoryRepository->findById($id);
    }

    public function updateProductSubCategory($id, array $data)
    {
        return $this->productSubCategoryRepository->update($id, $data);
    }

    public function deleteProductSubCategory($id)
    {
        return $this->productSubCategoryRepository->delete($id);
    }
}
