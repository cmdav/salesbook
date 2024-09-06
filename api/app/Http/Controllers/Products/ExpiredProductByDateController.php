<?php

namespace App\Http\Controllers\Products;

use App\Http\Controllers\Controller;
use App\Services\Products\ExpiredProductByDateService\ExpiredProductByDateService;
use App\Http\Requests\Products\ExpiredProductByDateFormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ExpiredProductByDateController extends Controller
{
    private $expiredProductByDateService;

    public function __construct(ExpiredProductByDateService $expiredProductByDateService)
    {
        $this->expiredProductByDateService = $expiredProductByDateService;
    }

    public function index(Request $request)
    {
        $data = $this->expiredProductByDateService->index($request);
        if (!$data->isEmpty()) {
            return response()->json(['success' => true, 'message' => 'Record retrieved successfully', 'data' => $data], 200);
        }
        return response()->json(['success' => false, 'message' => 'No record found'], 404);
    }
}
