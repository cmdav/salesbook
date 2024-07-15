<?php

namespace App\Http\Controllers\Inventory;
use App\Http\Controllers\Controller;
use App\Services\Inventory\SaleService\SaleService;
use App\Models\Sale;
use Illuminate\Http\Request;
use Exception;

class SalesRecieptController extends Controller
{
    protected $saleService;

    public function __construct(SaleService $saleService)
    {
        $this->saleService = $saleService;
    }
    public function show($transactionId,Request $request)
    {
        $sale = $this->saleService->downSalesReceipt($transactionId,$request);
        return response()->json(['data'=>$sale]);
    }

   
}
