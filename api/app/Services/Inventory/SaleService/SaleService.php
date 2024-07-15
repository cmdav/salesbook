<?php

namespace App\Services\Inventory\SaleService;
use App\Services\Inventory\SaleService\SaleRepository;


class SaleService 
{
    protected $saleRepository;

    public function __construct(SaleRepository $saleRepository)
    {
       $this->saleRepository = $saleRepository;
    }

    public function createSale(array $data)
    {
       
        return $this->saleRepository->create($data);
    }

    public function getAllSale($request)
    {
       
        return $this->saleRepository->index($request);
    }

    public function getSaleById($id)
    {
        return $this->saleRepository->findById($id);
    }

    public function updateSale($id, array $data)
    {
        return $this->saleRepository->update($id, $data);
    }

    public function deleteSale($id)
    {
        return $this->saleRepository->delete($id);
    }
    public function searchSale($id, $request)
    {
        return $this->saleRepository->searchSale($id, $request);
    }
    public function dailySale()
    {
        return $this->saleRepository->dailySale();
    }
    public function downSalesReceipt($TransactionId,$request)
    {
        return $this->saleRepository->downSalesReceipt($TransactionId,$request);
    }
}
