<?php

namespace App\Services\Inventory\SaleService;

use App\Models\Sale;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;

class SaleRepository 
{
    public function index()
    {
       
        return Sale::latest()->paginate(20);

    }
    public function create(array $data)
    {
       
        return Sale::create($data);
    }

    public function findById($id)
    {
        return Sale::find($id);
    }

    public function update($id, array $data)
    {
        $sale = $this->findById($id);
      
        if ($sale) {

            $sale->update($data);
        }
        return $sale;
    }

    public function delete($id)
    {
        $sale = $this->findById($id);
        if ($sale) {
            return $sale->delete();
        }
        return null;
    }
}
