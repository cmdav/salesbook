<?php

namespace App\Services\Product\ContainerTypeCapacityService;

use App\Services\Product\ContainerTypeCapacityService\ContainerTypeCapacityRepository;

class ContainerTypeCapacityService
{
    protected $containerTypeCapacityRepository;

    public function __construct(ContainerTypeCapacityRepository $containerTypeCapacityRepository)
    {
        $this->containerTypeCapacityRepository = $containerTypeCapacityRepository;
    }

    public function index()
    {
        return $this->containerTypeCapacityRepository->index();
    }

    public function show($id)
    {
        return $this->containerTypeCapacityRepository->show($id);
    }

    public function store($data)
    {
        return $this->containerTypeCapacityRepository->store($data);
    }

    public function update($data, $id)
    {
        return $this->containerTypeCapacityRepository->update($data, $id);
    }

    public function destroy($id)
    {
        return $this->containerTypeCapacityRepository->destroy($id);
    }
}
