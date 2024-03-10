<?php

namespace App\Http\Controllers\Inventory;
use App\Http\Controllers\Controller;
use App\Http\Requests\PriceFormRequest;
use App\Services\Inventory\PriceService\PriceService;
use Illuminate\Http\Request;

class PriceController extends Controller
{
     protected $priceService;

    public function __construct(PriceService $priceService)
    {
       $this->priceService = $priceService;
    }
    public function index()
    {
       
        $price =$this->priceService->getAllPrice();
        return response()->json($price);
    }

    public function Price(PriceFormRequest $request)
    {
        $price =$this->priceService->createPrice($request->all());
        return response()->json($price, 201);
    }

    public function show($id)
    {
        $price =$this->priceService->getPriceById($id);
        return response()->json($price);
    }

    public function update($id, Request $request)
    {
       
        $price =$this->priceService->updatePrice($id, $request->all());
        return response()->json($price);
    }

    public function destroy($id)
    {
       $this->priceService->deletePrice($id);
        return response()->json(null, 204);
    }
}
