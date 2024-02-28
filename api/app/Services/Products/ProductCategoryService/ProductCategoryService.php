<?php

namespace App\Services\Products\ProductCategoryService;
use App\Services\Products\ProductCategoryService\ProductCategoryRepository;


class ProductCategoryService 
{
    protected $productCategoryRepository;

    public function __construct(ProductCategoryRepository $productCategoryRepository)
    {
         $this->productCategoryRepository = $productCategoryRepository;
    }

    public function createProductCategory(array $data)
    {
       
        return  $this->productCategoryRepository->create($data);
    }

    public function getAllProductCategory()
    {
       
        return  $this->productCategoryRepository->index();
    }

    public function getProductCategoryById($id)
    {
        return  $this->productCategoryRepository->findById($id);
    }

    public function updateProductCategory($id, array $data)
    {
        return  $this->productCategoryRepository->update($id, $data);
    }

    public function deleteProductCategory($id)
    {
        return  $this->productCategoryRepository->delete($id);
    }
}
