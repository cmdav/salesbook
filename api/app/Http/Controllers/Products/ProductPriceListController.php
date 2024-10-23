<?php

namespace App\Http\Controllers\Products;

use App\Http\Controllers\Controller;
use App\Services\Products\ProductPriceListService\ProductPriceListService;
use App\Http\Requests\Products\ProductPriceListFormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProductPriceListController extends Controller
{
    private $productPriceListService;

    public function __construct(ProductPriceListService $productPriceListService)
    {
        $this->productPriceListService = $productPriceListService;
    }

    public function index(Request $request)
    {
        $response = $this->productPriceListService->index($request);
        if ($response["isPdf"]) {
            return $response["data"];
        } else {
            if(count($response["data"]) > 0) {
                return response()->json(['success' => true, 'message' => 'Record retrieved successfully', 'data' => $response["data"]], 200);
            }
            return response()->json(['success' => false, 'message' => 'No record found'], 404);
        }
        // if (!$data->isEmpty()) {
        //     return response()->json(['success' => true, 'message' => 'Record retrieved successfully', 'data' => $data], 200);
        // }
        // return response()->json(['success' => false, 'message' => 'No record found'], 404);
    }
}
