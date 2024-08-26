<?php

namespace App\Services\SellingUnit\ListPurchaseUnitService;

use App\Services\SellingUnit\PurchaseUnitService\PurchaseUnitRepository;

class ListPurchaseUnitService
{
    protected $purchaseUnitRepository;

    public function __construct(PurchaseUnitRepository $purchaseUnitRepository)
    {
        $this->purchaseUnitRepository = $purchaseUnitRepository;
    }

    public function index($data = null, $id = null)
    {

        return $this->purchaseUnitRepository->listPurchaseUnit($data, $id);
    }
}
