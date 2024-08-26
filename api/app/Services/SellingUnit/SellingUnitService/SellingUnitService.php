<?php

namespace App\Services\SellingUnit\SellingUnitService;

use App\Services\SellingUnit\SellingUnitService\SellingUnitRepository;

class SellingUnitService
{
    protected $sellingUnitRepository;

    public function __construct(SellingUnitRepository $sellingUnitRepository)
    {
        $this->sellingUnitRepository = $sellingUnitRepository;
    }

    public function index()
    {
        return $this->sellingUnitRepository->index();
    }

    public function show($id)
    {
        return $this->sellingUnitRepository->show($id);
    }

    public function store($data)
    {
        return $this->sellingUnitRepository->store($data);
    }

    public function update($data, $id)
    {
        return $this->sellingUnitRepository->update($data, $id);
    }

    public function destroy($id)
    {
        return $this->sellingUnitRepository->destroy($id);
    }
}
