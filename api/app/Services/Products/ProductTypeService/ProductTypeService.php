<?php

namespace App\Services\Products\ProductTypeService;
use App\Services\Products\ProductTypeService\ProductTypeRepository;


class ProductTypeService 
{
    protected $ProductTypeRepository;

    public function __construct(ProductTypeRepository $ProductTypeRepository)
    {
        $this->ProductTypeRepository = $ProductTypeRepository;
    }

    public function create(array $data)
    {
       
        return $this->ProductTypeRepository->create($data);
    }

    public function index()
    {
       
        return $this->ProductTypeRepository->index();
    }

    public function getProductTypeById($id)
    {
        return $this->ProductTypeRepository->findById($id);
    }

    public function update($id, array $data)
    {
        return $this->ProductTypeRepository->update($id, $data);
    }

    public function deleteProductType($id)
    {
        return $this->ProductTypeRepository->delete($id);
    }
}
