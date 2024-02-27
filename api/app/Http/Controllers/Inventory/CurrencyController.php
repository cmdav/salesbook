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
        $currency = $this->currencyService->createcurrency($request->all());
        return response()->json($currency, 201);
    }

    public function show($id)
    {
        $currency = $this->currencyService->getcurrencyById($id);
        return response()->json($currency);
    }

    public function update($id, Request $request)
    {
       
        $currency = $this->currencyService->updateCurrency($id, $request->all());
        return response()->json($currency);
    }

    public function destroy($id)
    {
        $this->currencyService->deleteCurrency($id);
        return response()->json(null, 204);
    }
}
