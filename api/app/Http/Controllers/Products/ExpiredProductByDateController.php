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
        $response = $this->expiredProductByDateService->index($request);
        if ($response["isPdf"]) {
            return $response["data"];
        } else {
            if(count($response["data"]) > 0) {
                return response()->json(['success' => true, 'message' => 'Record retrieved successfully', 'data' => $response["data"]], 200);
            }
            return response()->json(['success' => false, 'message' => 'No record found'], 404);
        }
        // if (!$data->isEmpty()) {

        //     return $data;

        // }
        // return response()->json(['success' => false, 'message' => 'No record found'], 404);
    }
}
