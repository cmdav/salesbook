<?php

namespace App\Services\Product\ContainerTypeService;

use App\Services\Product\ContainerTypeService\ContainerTypeRepository;

class ContainerTypeService
{
    protected $containerTypeRepository;

    public function __construct(ContainerTypeRepository $containerTypeRepository)
    {
        $this->containerTypeRepository = $containerTypeRepository;
    }

    public function index()
    {
        return $this->containerTypeRepository->index();
    }

    public function show($id)
    {
        return $this->containerTypeRepository->show($id);
    }

    public function store($data)
    {
        return $this->containerTypeRepository->store($data);
    }

    public function update($data, $id)
    {
        return $this->containerTypeRepository->update($data, $id);
    }

    public function destroy($id)
    {
        return $this->containerTypeRepository->destroy($id);
    }
}
