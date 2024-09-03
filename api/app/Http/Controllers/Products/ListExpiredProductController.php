<?php

namespace App\Http\Controllers\Products;
use App\Http\Controllers\Controller;
use App\Services\Products\ListExpiredProductService\ListExpiredProductService;
use App\Http\Requests\Products\ListExpiredProductFormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ListExpiredProductController extends Controller
{
    private $listExpiredProductService;

    public function __construct(ListExpiredProductService $listExpiredProductService)
    {
        $this->listExpiredProductService = $listExpiredProductService;
    }

    public function index()
    {
        $data = $this->listExpiredProductService->index();
         if (!$data->isEmpty()) {
            return response()->json(['success' => true, 'message' => 'Record retrieved successfully', 'data' => $data], 200);
        }
        return response()->json(['success' => false, 'message' => 'No record found'], 404);
    }
}