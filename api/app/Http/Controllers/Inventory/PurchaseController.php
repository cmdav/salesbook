<?php

namespace App\Http\Controllers\Inventory;
use App\Http\Controllers\Controller;
use App\Http\Requests\PurchaseFormRequest;
use App\Services\Inventory\PurchaseService\PurchaseService;
use App\Models\Purchase;
use Illuminate\Http\Request;

class PurchaseController extends Controller
{
     protected $purchaseService;

    public function __construct(PurchaseService $purchaseService)
    {
       $this->purchaseService = $purchaseService;
    }
    public function index(Request $request)
    {
        
        $purchase =$this->purchaseService->getAllPurchase($request->all());
        return response()->json($purchase);
    }
    
    public function store(PurchaseFormRequest $request)
    {
        return $this->purchaseService->createPurchase($request->all());
       
    }

    public function show($id)
    {
        $purchase =$this->purchaseService->getPurchaseById($id);
        return response()->json($purchase);
    }

    public function update($id, Request $request)
    {
       
        $purchase =$this->purchaseService->updatePurchase($id, $request->all());
        return response()->json($purchase);
    }

    public function destroy($id)
    {
       $this->purchaseService->deletePurchase($id);
        return response()->json(null, 204);
    }
}
