<?php

namespace App\Services\Inventory\InventoryService;
use App\Services\Inventory\InventoryService\InventoryRepository;


class InventoryService 
{
    protected $inventoryRepository;

    public function __construct(InventoryRepository $inventoryRepository)
    {
       $this->InventoryRepository = $inventoryRepository;
    }

    public function createInventory(array $data)
    {
       
        return $this->InventoryRepository->create($data);
    }

    public function getAllInventory()
    {
       
        return $this->InventoryRepository->index();
    }

    public function getInventoryById($id)
    {
        return $this->InventoryRepository->findById($id);
    }

    public function updateInventory($id, array $data)
    {
        return $this->InventoryRepository->update($id, $data);
    }

    public function deleteInventory($id)
    {
        return $this->InventoryRepository->delete($id);
    }
}
