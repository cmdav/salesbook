<?php

namespace App\Services\Inventory\PurchaseService;
use App\Services\Inventory\PurchaseService\PurchaseRepository;


class PurchaseService 
{
    protected $PurchaseRepository;

    public function __construct(PurchaseRepository $PurchaseRepository)
    {
        $this->PurchaseRepository = $PurchaseRepository;
    }

    public function createPurchase(array $data)
    {
       
        return $this->PurchaseRepository->create($data);
    }

    public function getAllPurchase($request)
    {
       
        return $this->PurchaseRepository->index($request);
    }

    public function getPurchaseById($id)
    {
        return $this->PurchaseRepository->findById($id);
    }

    public function updatePurchase($id, array $data)
    {
        return $this->PurchaseRepository->update($id, $data);
    }

    public function deletePurchase($id)
    {
        return $this->PurchaseRepository->delete($id);
    }
    public function searchPurchase($id, $request)
    {
        return $this->PurchaseRepository->searchPurchase($id, $request);
    }
}
