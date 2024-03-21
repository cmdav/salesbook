<?php

namespace App\Services\Inventory\PriceService;
use App\Services\Inventory\PriceService\PriceRepository;


class PriceService 
{
    protected $PriceRepository;

    public function __construct(PriceRepository $PriceRepository)
    {
        $this->PriceRepository = $PriceRepository;
    }

    public function createPrice(array $data)
    {
       
        return $this->PriceRepository->create($data);
    }

    public function getAllPrice()
    {
       
        return $this->PriceRepository->index();
    }

    public function show($id)
    {
        return $this->PriceRepository->findById($id);
    }
    public function getPriceByProductType($id)
    {
        return $this->PriceRepository->getPriceByProductType($id);
    }
    public function getAllPriceByProductType($id)
    {
        return $this->PriceRepository->getAllPriceByProductType($id);
    }
    public function getLatestPriceByProductType($id)
    {
        return $this->PriceRepository->getLatestPriceByProductType($id);
    }

    public function updatePrice($id, array $data)
    {
        return $this->PriceRepository->update($id, $data);
    }

    public function deletePrice($id)
    {
        return $this->PriceRepository->delete($id);
    }
}
