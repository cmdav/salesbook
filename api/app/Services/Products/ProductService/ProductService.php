<?php

namespace App\Services\Products\ProductService;
use App\Services\Products\ProductService\ProductRepository;


class ProductService 
{
    protected $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function createProduct(array $data)
    {
       
        return $this->productRepository->create($data);
    }

    public function getAllProduct()
    {
       
        return $this->productRepository->index();
    }
    public function listAllProduct()
    {
       
        return $this->productRepository->listAllProduct();
    }
   


    public function getProductById($id)
    {
        return $this->productRepository->findById($id);
    }

    public function updateProduct($id, array $data)
    {
        return $this->productRepository->update($id, $data);
    }

    public function deleteProduct($id)
    {
        return $this->productRepository->delete($id);
    }
}
