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
    public function index()
    {
      
        $price =$this->priceNotificationService->index();
        return response()->json($price);
    }

    public function store(PriceFormRequest $request)
    {
        $price =$this->priceNotificationService->createPrice($request->all());
        return response()->json($price, 201);
    }

    public function show($id)
    {
        $price =$this->priceNotificationService->show($id);
        return response()->json($price);
    }
    public function getPriceByProductType($id)
    {
        return $this->priceNotificationService->getPriceByProductType($id);
    }

    public function update($id, PriceFormRequest $request)
    {
       
        $price =$this->priceNotificationService->updatePrice($id, $request->all());
        return response()->json($price);
    }

    public function destroy($id)
    {
       $this->priceNotificationService->deletePrice($id);
        return response()->json(null, 204);
    }
}
