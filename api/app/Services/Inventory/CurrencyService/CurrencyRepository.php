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
       try{
        $data=Currency::create($data);
        return response()->json([
            'success' => true,
            'message' => 'Currency created successfully',
            'data' => $data,
        ], 200);
    } catch (Exception $e) {
        Log::channel('insertion_errors')->error('Error creating or updating user: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'This currency could not be  created',
        ], 500);
      

    }
      
    }

    public function findById($id)
    {
        return Currency::find($id);
    }

    public function update($id, array $data)
    {
        $Currency = $this->findById($id);
      try{
        if ($Currency) {

            $data  =$Currency->update($data);
            return response()->json([
                'success' => true,
                'message' => 'Update successful',
                'data' => $data,
            ], 200);
        }
    } catch (Exception $e) {
        Log::channel('insertion_errors')->error('Error creating or updating user: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'This currency could not be updated',
        ], 500);
      

    }
    }

    public function delete($id)
{
    try {
        $Currency = $this->findById($id);

        if ($Currency) {
            if (strtolower($Currency->currency_name) === 'naira') {
                return response()->json([
                    'success' => false,
                    'message' => 'This Currency is already in use.',
                ], 403); 
            }

            $Currency->delete();
            return response()->json([
                'success' => true,
                'message' => 'Deletion successful',
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Currency not found',
            ], 404); // Not Found status code
        }
    } catch (Exception $e) {
        Log::channel('insertion_errors')->error('Error deleting currency: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'This currency could not be deleted',
        ], 500); // Internal Server Error status code
    }
}

}
