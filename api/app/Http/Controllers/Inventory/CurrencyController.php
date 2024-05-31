<?php

namespace App\Http\Controllers\Inventory;
use App\Http\Controllers\Controller;
use App\Http\Requests\CurrencyFormRequest;
use App\Services\Inventory\CurrencyService\CurrencyService;
use App\Models\Currency;
use Illuminate\Http\Request;

class CurrencyController extends Controller
{
      protected $currencyService;

    public function __construct(currencyService $currencyService)
    {
        $this->currencyService = $currencyService;
    }
    public function index()
    {
        $currency = $this->currencyService->getAllcurrency();
        return response()->json($currency);
    }

    public function store(CurrencyFormRequest $request)
    {
        return $this->currencyService->createcurrency($request->all());
       
    }

    public function show($id)
    {
        $currency = $this->currencyService->getcurrencyById($id);
        return response()->json($currency);
    }

    public function update($id, CurrencyFormRequest $request)
    {
       
        return $this->currencyService->updateCurrency($id, $request->all());
      
    }

    public function destroy($id)
    {
        return $this->currencyService->deleteCurrency($id);
       
    }
}
