<?php

namespace App\Services\Supply\SupplierProductService;
use App\Services\Supply\SupplierProductService\SupplierProductRepository;


class SupplierProductService 
{
    protected $supplierProductRepository;

    public function __construct(SupplierProductRepository $supplierProductRepository)
    {
        $this->supplierProductRepository = $supplierProductRepository;
    }

    public function createSupplierProduct(array $data)
    {
       
        return $this->supplierProductRepository->create($data);
    }

    public function getAllSupplierProduct()
    {
       
        return $this->supplierProductRepository->index();
    }
    public function getAuthSupplierProduct()
    {
       
        return $this->supplierProductRepository->getAuthSupplierProduct();
    }
    public function listAllSupplierProduct()
    {
       
        return $this->supplierProductRepository->listAllSupplierProduct();
    }
    public function productSuppliedToCompany()
    {
       
        return $this->supplierProductRepository->productSuppliedToCompany();
    }
    public function getSupplierProductById($id)
    {
        return $this->supplierProductRepository->findById($id);
    }

    public function updateSupplierProduct($id, array $data)
    {
        return $this->supplierProductRepository->update($id, $data);
    }

    public function deleteSupplierProduct($id)
    {
        return $this->supplierProductRepository->delete($id);
    }
}
