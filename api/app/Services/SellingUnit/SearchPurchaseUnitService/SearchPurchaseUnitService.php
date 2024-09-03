<?php

namespace App\Services\SellingUnit\SearchPurchaseUnitService;

use App\Services\SellingUnit\PurchaseUnitService\PurchaseUnitRepository;

class SearchPurchaseUnitService
{
    protected $purchaseUnitRepository;

    public function __construct(PurchaseUnitRepository $purchaseUnitRepository)
    {
        $this->purchaseUnitRepository = $purchaseUnitRepository;
    }

    
    public function index($data = null, $id = null)
    {
        return $this->purchaseUnitRepository->getsearchPurchaseUnit($data, $id);
    }
}
