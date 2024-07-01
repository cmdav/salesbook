<?php

namespace App\Services\Supply\SupplierService;
use App\Services\Supply\SupplierService\SupplierRepository;


class SupplierService 
{
    protected $SupplierRepository;

    public function __construct(SupplierRepository $SupplierRepository)
    {
        $this->SupplierRepository = $SupplierRepository;
    }

    public function createSupplier(array $data)
    {
       
        return $this->SupplierRepository->create($data);
    }

    public function getAllSupplier($request)
    {
       
        return $this->SupplierRepository->index($request);
    }

    public function getSupplierById($id)
    {
        return $this->SupplierRepository->findById($id);
    }

    public function updateSupplier($id, array $data)
    {
        return $this->SupplierRepository->update($id, $data);
    }

    public function deleteSupplier($id)
    {
        return $this->SupplierRepository->delete($id);
    }
}
