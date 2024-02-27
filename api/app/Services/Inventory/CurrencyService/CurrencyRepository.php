<?php

namespace App\Services\Inventory\CurrencyService;

use App\Models\Currency;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;

class CurrencyRepository 
{
    public function index()
    {
       
        return Currency::select("id", "currency_name","currency_symbol")->latest()->get();

    }
    public function create(array $data)
    {
       
        return Currency::create($data);
    }

    public function findById($id)
    {
        return Currency::find($id);
    }

    public function update($id, array $data)
    {
        $Currency = $this->findById($id);
      
        if ($Currency) {

            $Currency->update($data);
        }
        return $Currency;
    }

    public function delete($id)
    {
        $Currency = $this->findById($id);
        if ($Currency) {
            return $Currency->delete();
        }
        return null;
    }
}
