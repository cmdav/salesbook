<?php

namespace App\Http\Controllers\SellingUnit;

use App\Http\Controllers\Controller;
use App\Services\SellingUnit\ListPurchaseUnitService\ListPurchaseUnitService;
use App\Http\Requests\SellingUnit\ListPurchaseUnitFormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ListPurchaseUnitController extends Controller
{
    private $listPurchaseUnitService;

    public function __construct(ListPurchaseUnitService $listPurchaseUnitService)
    {
        $this->listPurchaseUnitService = $listPurchaseUnitService;
    }

    public function index()
    {

        $data = $this->listPurchaseUnitService->index();
        if ($data) {
            return response()->json(['success' => true, 'message' => 'Record retrieved successfully', 'data' => $data], 200);
        }
        return response()->json(['success' => false, 'message' => 'No record found'], 404);
    }
}
