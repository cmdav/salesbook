<?php

namespace App\Services\Product\ListAllContainerService;

use App\Services\Product\ContainerTypeService\ContainerTypeRepository;

class ListAllContainerService
{
    protected $containerTypeRepository;

    public function __construct(ContainerTypeRepository $containerTypeRepository)
    {
        $this->containerTypeRepository = $containerTypeRepository;
    }

    public function index($data = null, $id = null)
    {
        return $this->containerTypeRepository->listAllContainer($data, $id);
    }
}
