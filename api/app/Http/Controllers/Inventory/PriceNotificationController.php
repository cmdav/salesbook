<?php

namespace App\Http\Controllers\Inventory;
use App\Http\Controllers\Controller;
use App\Http\Requests\PriceFormRequest;
use App\Services\Inventory\PriceService\PriceNotificationService;
use Illuminate\Http\Request;

class PriceNotificationController extends Controller
{
     protected $priceNotificationService;

    public function __construct(PriceNotificationService $priceNotificationService)
    {
       $this->priceNotificationService = $priceNotificationService;
    }
    public function index(Request $request)
    {
      
        $price =$this->priceNotificationService->index($request->all());
        return response()->json($price);
    }

    public function store(Request $request)
    {
        $request->merge(['status' => 0]);
        $request->validate([
            'supplier_id' => 'required|uuid',
            'product_type_id' => 'required|uuid',
            'cost_price' => 'required|integer',
        ]);
        
        return $this->priceNotificationService->createPrice($request->all());
       
    }

    public function show($id)
    {
        $price =$this->priceNotificationService->show($id);
        return response()->json($price);
    }
   
    
    public function update($id, Request $request)
    {
        $request->validate([
            'selling_price' => 'integer|required_unless:status,decline',
            'status' => 'required|string|in:pending,accepted,decline',
        ]);
        
        $price =$this->priceNotificationService->updatePrice($id, $request->all());
        return response()->json($price);
    }

    public function destroy($id)
    {
       $this->priceNotificationService->deletePrice($id);
        return response()->json(null, 204);
    }
}
