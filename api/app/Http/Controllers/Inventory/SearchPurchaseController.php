<?php

namespace App\Http\Controllers\Inventory;
use App\Http\Controllers\Controller;
use App\Http\Requests\PurchaseFormRequest;
use App\Services\Inventory\PurchaseService\PurchaseService;
use App\Models\Purchase;
use Illuminate\Http\Request;

class SearchPurchaseController extends Controller
{
     protected $purchaseService;

    public function __construct(PurchaseService $purchaseService)
    {
       $this->purchaseService = $purchaseService;
    }
  
    public function show($id)
    {
        $purchase =$this->purchaseService->searchPurchase($id);
        return response()->json($purchase);
    }

    
}
