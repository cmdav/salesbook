<?php

namespace App\Services\Security\BusinessBranchService;

use App\Services\Security\BusinessBranchService\BusinessBranchRepository;

class BusinessBranchService
{
    protected $businessBranchRepository;

    public function __construct(BusinessBranchRepository $businessBranchRepository)
    {
        $this->businessBranchRepository = $businessBranchRepository;
    }

    public function index()
    {
        return $this->businessBranchRepository->index();
    }

    public function show($id)
    {
        return $this->businessBranchRepository->show($id);
    }

    public function store($data)
    {
        return $this->businessBranchRepository->store($data);
    }

    public function update($data, $id)
    {
        return $this->businessBranchRepository->update($data, $id);
    }

    public function destroy($id)
    {
        return $this->businessBranchRepository->destroy($id);
    }
    public function listing()
    {
        return $this->businessBranchRepository->listing();
    }
}
