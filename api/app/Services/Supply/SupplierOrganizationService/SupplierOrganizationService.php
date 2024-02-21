<?php

namespace App\Services\Supply\SupplierOrganizationService;
use App\Services\Supply\SupplierOrganizationService\SupplierOrganizationRepository;


class SupplierOrganizationService 
{
    protected $SupplierOrganizationRepository;

    public function __construct(SupplierOrganizationRepository $SupplierOrganizationRepository)
    {
        $this->SupplierOrganizationRepository = $SupplierOrganizationRepository;
    }

    public function createSupplierOrganization(array $data)
    {
      
        return $this->SupplierOrganizationRepository->create($data);
    }

    public function getAllSupplierOrganization()
    {
       
        return $this->SupplierOrganizationRepository->index();
    }

    public function getSupplierOrganizationById($id)
    {
        return $this->SupplierOrganizationRepository->findById($id);
    }

    public function updateSupplierOrganization($id, array $data)
    {
        return $this->SupplierOrganizationRepository->update($id, $data);
    }

    public function deleteSupplierOrganization($id)
    {
        return $this->SupplierOrganizationRepository->delete($id);
    }
    public function updateSupplierStatus($organization_id, $supplier_id){

        return $this->SupplierOrganizationRepository->updateSupplierStatus($organization_id, $supplier_id);
    }
}
