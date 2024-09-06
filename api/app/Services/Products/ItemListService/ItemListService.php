<?php

namespace App\Services\Products\ItemListService;

use App\Services\Inventory\StoreService\StoreRepository;

class ItemListService
{
    protected $storeRepository;

    public function __construct(StoreRepository $storeRepository)
    {
        $this->storeRepository = $storeRepository;
    }


    public function index($data = null, $id = null)
    {

        return $this->storeRepository->getitemList($data, $id);
    }
}
