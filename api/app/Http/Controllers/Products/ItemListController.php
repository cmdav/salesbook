<?php

namespace App\Http\Controllers\Products;

use App\Http\Controllers\Controller;
use App\Services\Products\ItemListService\ItemListService;
use App\Http\Requests\Products\ItemListFormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ItemListController extends Controller
{
    private $itemListService;

    public function __construct(ItemListService $itemListService)
    {
        $this->itemListService = $itemListService;
    }

    public function index(Request $request)
    {
        $response = $this->itemListService->index($request->all());
        //return $response["isPdf"];

        if ($response["isPdf"]) {
            return $response["data"];
        } else {
            if(count($response["data"]) > 0) {
                return response()->json(['success' => true, 'message' => 'Record retrieved successfully', 'data' => $response["data"]], 200);
            }
            return response()->json(['success' => false, 'message' => 'No record found'], 404);
        }

    }
}
