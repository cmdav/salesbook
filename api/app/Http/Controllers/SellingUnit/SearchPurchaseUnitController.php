<?php

namespace App\Http\Controllers\SellingUnit;

use App\Http\Controllers\Controller;
use App\Services\SellingUnit\SearchPurchaseUnitService\SearchPurchaseUnitService;
use App\Http\Requests\SellingUnit\SearchPurchaseUnitFormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SearchPurchaseUnitController extends Controller
{
    private $searchPurchaseUnitService;

    public function __construct(SearchPurchaseUnitService $searchPurchaseUnitService)
    {
        $this->searchPurchaseUnitService = $searchPurchaseUnitService;
    }

    public function index(Request $request)
    {

        $data = $this->searchPurchaseUnitService->index($request['search']);
        if (!$data->isEmpty()) {
            return response()->json(['success' => true, 'message' => 'Record retrieved successfully', 'data' => $data], 200);
        }
        return response()->json([], 200);
    }
}
