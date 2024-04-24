<?php

namespace App\Services\Inventory\CurrencyService;

use App\Models\Currency;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;

class CurrencyRepository 
{
    private function query(){

        return Currency::select("id", "currency_name","currency_symbol");
    }
    public function index()
    {
        
        $currencies =Currency::select("id", "currency_name","currency_symbol","status", "created_by","updated_by")->with('creator','updater')->get();
       
        $transformed = $currencies->map(function($currency) {
            return [
                'id' => $currency->id,
                'currency_name' => $currency->currency_name,
                'currency_symbol' => $currency->currency_symbol,
                'status' => $currency->status,
                'created_by' => $currency->creator->fullname ?? '',  
                'updated_by' => $currency->updater->fullname ?? ''
            ];
        });

        return $transformed;
       

    }
    public function searchCurrency($searchCriteria){

        return $this->query()->where('currency_name', 'like', '%' . $searchCriteria . '%')->latest()->get();
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
