<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Http\Requests\PriceFormRequest;
use App\Services\Inventory\PriceService\PriceService;
use Illuminate\Http\Request;

class LatestSupplierPriceController extends Controller
{
    protected $priceService;

    public function __invoke(PriceService $priceService, $product_type_id, $supplier_id, $purchase_unit_id, Request $request)
    {
        $this->priceService = $priceService;
        $validated = $request->validate([
            'mode' => ['required', 'in:actual,estimate']  // Ensures that 'mode' is either 'actual' or 'estimate'
        ]);

        return $this->priceService->getLatestSupplierPrice($product_type_id, $supplier_id, $purchase_unit_id, $request);
    }


}
