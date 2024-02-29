<?php

namespace App\Services\Inventory\InventoryService;

use App\Models\Inventory;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;

class InventoryRepository 
{
    public function index()
    {
       
        return Inventory::latest()->paginate(20);

    }
    public function create(array $data)
    {
       
        return Inventory::create($data);
    }

    public function findById($id)
    {
        return Inventory::find($id);
    }

    public function update($id, array $data)
    {
        $inventory = $this->findById($id);
      
        if ($inventory) {

            $inventory->update($data);
        }
        return $inventory;
    }

    public function delete($id)
    {
        $inventory = $this->findById($id);
        if ($inventory) {
            return $inventory->delete();
        }
        return null;
    }
}
