<?php

namespace App\Services\Security\PermissionServices;
use App\Services\Security\PermissionServices\PermissionRepository;


class PermissionService 
{
    protected $PermissionRepository;

    public function __construct(PermissionRepository $PermissionRepository)
    {
        $this->PermissionRepository = $PermissionRepository;
    }

    public function create(array $data)
    {
       
        return $this->PermissionRepository->create($data);
    }

    public function Index()
    {
       
        return $this->PermissionRepository->index();
    }

    public function show($id)
    {
        return $this->PermissionRepository->findById($id);
    }
   
    public function update($id, array $data)
    {
        return $this->PermissionRepository->update($id, $data);
    }

    public function delete($id)
    {
        return $this->PermissionRepository->delete($id);
    }
}
