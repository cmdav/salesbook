<?php

namespace App\Services\SellingUnit\PurchaseUnitService;

use App\Services\SellingUnit\PurchaseUnitService\PurchaseUnitRepository;

class PurchaseUnitService
{
    protected $purchaseUnitRepository;

    public function __construct(PurchaseUnitRepository $purchaseUnitRepository)
    {
        $this->purchaseUnitRepository = $purchaseUnitRepository;
    }

    public function index()
    {
        return $this->purchaseUnitRepository->index();
    }


    public function show($id)
    {
        return $this->purchaseUnitRepository->show($id);
    }

    public function store($data)
    {
        return $this->purchaseUnitRepository->store($data);
    }

    public function update($data, $id)
    {
        return $this->purchaseUnitRepository->update($data, $id);
    }

    public function destroy($id)
    {
        return $this->purchaseUnitRepository->destroy($id);
    }
}
