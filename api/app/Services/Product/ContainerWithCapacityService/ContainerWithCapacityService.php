<?php

namespace App\Services\Product\ContainerWithCapacityService;

use App\Services\Product\ContainerTypeService\ContainerTypeRepository;

class ContainerWithCapacityService
{
    protected $containerTypeRepository;

    public function __construct(ContainerTypeRepository $containerTypeRepository)
    {
        $this->containerTypeRepository = $containerTypeRepository;
    }

    public function show($data = null, $id = null)
    {
        return $this->containerTypeRepository->containerWithCapacity($data, $id);
    }
}
