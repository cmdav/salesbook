<?php

namespace App\Services\Inventory\StoreService;
use App\Services\Inventory\StoreService\StoreRepository;


class StoreService 
{
    protected $storeRepository;

    public function __construct(StoreRepository $storeRepository)
    {
        $this->storeRepository = $storeRepository;
    }

    public function createStore(array $data)
    {
       
        return $this->storeRepository->create($data);
    }

    public function getAllStore()
    {
       
        return $this->storeRepository->index();
    }

    public function getStoreById($id)
    {
        return $this->storeRepository->findById($id);
    }

    public function updateStore($id, array $data)
    {
        return $this->storeRepository->update($id, $data);
    }

    public function deleteStore($id)
    {
        return $this->storeRepository->delete($id);
    }
}
