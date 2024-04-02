<?php

namespace App\Services\Security\JobRoleServices;
use App\Services\Security\JobRoleServices\JobRoleRepository;


class JobRoleService 
{
    protected $JobRoleRepository;

    public function __construct(JobRoleRepository $JobRoleRepository)
    {
        $this->JobRoleRepository = $JobRoleRepository;
    }

    public function create(array $data)
    {
       
        return $this->JobRoleRepository->create($data);
    }

    public function Index()
    {
       
        return $this->JobRoleRepository->index();
    }

    public function show($id)
    {
        return $this->JobRoleRepository->findById($id);
    }
   
    public function update($id, array $data)
    {
        return $this->JobRoleRepository->update($id, $data);
    }

    public function delete($id)
    {
        return $this->JobRoleRepository->delete($id);
    }
    public function names()
    {
        return $this->JobRoleRepository->names();
    }
}
