<?php

namespace App\Http\Controllers\Inventory;
use App\Http\Controllers\Controller;
use App\Http\Requests\PriceFormRequest;
use App\Services\Inventory\PriceService\PriceService;
use Illuminate\Http\Request;

class LatestPriceByProductTypeController extends Controller
{
     protected $priceService;

    public function __invoke(PriceService $priceService, $id)
    {
       $this->priceService = $priceService;
       return $this->priceService->getLatestPriceByProductType($id);
    }
    
   
}
