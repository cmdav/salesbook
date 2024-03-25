<?php

namespace App\Http\Controllers\Inventory;
use App\Http\Controllers\Controller;
use App\Services\Inventory\CurrencyService\CurrencyService;
use App\Models\Currency;
use Illuminate\Http\Request;

class SearchCurrencyController extends Controller
{
      protected $currencyService;

    public function __construct(currencyService $currencyService)
    {
        $this->currencyService = $currencyService;
    }
    public function show($search)
    {
       
        $currency = $this->currencyService->searchCurrency($search);
        return response()->json($currency);
    }

  
}
