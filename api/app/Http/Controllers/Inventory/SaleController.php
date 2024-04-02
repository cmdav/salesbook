<?php

namespace App\Http\Controllers\Inventory;
use App\Http\Controllers\Controller;
use App\Http\Requests\SaleFormRequest;
use App\Services\Inventory\SaleService\SaleService;
use App\Models\Sale;
use Illuminate\Http\Request;
use Exception;

class SaleController extends Controller
{
    protected $saleService;

    public function __construct(SaleService $saleService)
    {
        $this->saleService = $saleService;
    }
    public function index()
    {
        $sale = $this->saleService->getAllSale();
        return response()->json($sale);
    }

    public function store(SaleFormRequest $request)
    {
        
        try {
            $sale = $this->saleService->createSale($request->all());
            return response()->json(['message'=>'Sales recorded successfully'], 201);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        $sale = $this->saleService->getSaleById($id);
        return response()->json($sale);
    }

    public function update($id, Request $request)
    {
       
        $sale = $this->saleService->updateSale($id, $request->all());
        return response()->json($sale);
    }

    public function destroy($id)
    {
        $this->saleService->deleteSale($id);
        return response()->json(null, 204);
    }
}
