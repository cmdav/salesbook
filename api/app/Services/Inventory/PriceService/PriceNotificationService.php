<?php

namespace App\Services\Inventory\PriceService;
use App\Services\Inventory\PriceService\PriceNotificationRepository;


class PriceNotificationService 
{
    protected $PriceNotificationRepository;

    public function __construct(PriceNotificationRepository $PriceNotificationRepository)
    {
        $this->PriceNotificationRepository = $PriceNotificationRepository;
    }

    public function createPrice(array $data)
    {
       
        return $this->PriceNotificationRepository->create($data);
    }

    public function index()
    {
       
        return $this->PriceNotificationRepository->index();
    }

    public function show($id)
    {
        return $this->PriceNotificationRepository->findById($id);
    }
    public function getPriceByProductType($id)
    {
        return $this->PriceNotificationRepository->getPriceByProductType($id);
    }
    public function getAllPriceByProductType($id)
    {
        return $this->PriceNotificationRepository->getAllPriceByProductType($id);
    }
    public function getLatestPriceByProductType($id)
    {
        return $this->PriceNotificationRepository->getLatestPriceByProductType($id);
    }
    public function getLatestSupplierPrice($product_type_id, $supplier_id)
    {
        return $this->PriceNotificationRepository->getLatestSupplierPrice($product_type_id, $supplier_id);
    }
    public function updatePrice($id, array $data)
    {
        return $this->PriceNotificationRepository->update($id, $data);
    }

    public function deletePrice($id)
    {
        return $this->PriceNotificationRepository->delete($id);
    }
}
