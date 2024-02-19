<?php

namespace App\Services\Inventory\OrganizationService;
use App\Services\Inventory\OrganizationService\OrganizationRepository;


class OrganizationService 
{
    protected $organizationRepository;

    public function __construct(OrganizationRepository $organizationRepository)
    {
        $this->organizationRepository = $organizationRepository;
    }

    public function createOrganization(array $data)
    {
       
        return $this->organizationRepository->create($data);
    }

    public function getAllOrganization()
    {
       
        return $this->organizationRepository->index();
    }

    public function getOrganizationById($id)
    {
        return $this->organizationRepository->findById($id);
    }

    public function updateOrganization($id, array $data)
    {
        return $this->organizationRepository->update($id, $data);
    }

    public function deleteOrganization($id)
    {
        return $this->organizationRepository->delete($id);
    }
}
