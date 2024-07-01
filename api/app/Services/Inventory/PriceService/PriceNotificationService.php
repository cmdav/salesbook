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

    public function index($request)
    {
       
        return $this->PriceNotificationRepository->index($request);
    }

    public function show($id)
    {
        return $this->PriceNotificationRepository->show($id);
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
