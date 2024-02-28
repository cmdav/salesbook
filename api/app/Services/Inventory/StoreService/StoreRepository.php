<?php

namespace App\Services\Inventory\StoreService;

use App\Models\Store;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;

class StoreRepository 
{
    public function index()
    {
       
        return Store::latest()->paginate(20);

    }
    public function create(array $data)
    {
       
        return Store::create($data);
    }

    public function findById($id)
    {
        return Store::find($id);
    }

    public function update($id, array $data)
    {
        $store = $this->findById($id);
      
        if ($store) {

            $store->update($data);
        }
        return $store;
    }

    public function delete($id)
    {
        $store = $this->findById($id);
        if ($store) {
            return $store->delete();
        }
        return null;
    }
}
