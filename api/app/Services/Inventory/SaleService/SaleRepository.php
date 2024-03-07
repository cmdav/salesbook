<?php

namespace App\Services\Inventory\SaleService;

use App\Models\Sale;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;

class SaleRepository 
{
    public function index()
    {
       
        return Sale::with('store','organization:id,organization_name,organization_logo')->latest()->paginate(3);

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
