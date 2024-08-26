<?php

namespace App\Services\SellingUnit\SellingUnitCapacityService;

use App\Services\SellingUnit\SellingUnitCapacityService\SellingUnitCapacityRepository;

class SellingUnitCapacityService
{
    protected $sellingUnitCapacityRepository;

    public function __construct(SellingUnitCapacityRepository $sellingUnitCapacityRepository)
    {
        $this->sellingUnitCapacityRepository = $sellingUnitCapacityRepository;
    }

    public function index()
    {
        return $this->sellingUnitCapacityRepository->index();
    }

    public function show($id)
    {
        return $this->sellingUnitCapacityRepository->show($id);
    }

    public function store($data)
    {
        return $this->sellingUnitCapacityRepository->store($data);
    }

    public function update($data, $id)
    {
        return $this->sellingUnitCapacityRepository->update($data, $id);
    }

    public function destroy($id)
    {
        return $this->sellingUnitCapacityRepository->destroy($id);
    }
}
