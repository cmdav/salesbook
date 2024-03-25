<?php

namespace App\Http\Controllers\Inventory;
use App\Http\Controllers\Controller;
use App\Http\Requests\SaleFormRequest;
use App\Services\Inventory\SaleService\SaleService;
use App\Models\Sale;
use Illuminate\Http\Request;
use Exception;

class DailySaleController extends Controller
{
    protected $saleService;

    public function __construct(SaleService $saleService)
    {
        $this->saleService = $saleService;
    }
    public function index()
    {
        $sale = $this->saleService->dailySale();
        return response()->json($sale);
    }

   
}
