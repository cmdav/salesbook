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
        $data = $this->itemListService->index($request->all());
        if (!$data->isEmpty()) {
            return response()->json(['success' => true, 'message' => 'Record retrieved successfully', 'data' => $data], 200);
        }
        return response()->json(['success' => false, 'message' => 'No record found'], 404);
    }
}
