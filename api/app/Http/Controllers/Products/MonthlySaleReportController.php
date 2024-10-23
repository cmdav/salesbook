<?php

namespace App\Http\Controllers\Products;

use App\Http\Controllers\Controller;
use App\Services\Products\MonthlySaleReportService\MonthlySaleReportService;
use App\Http\Requests\Products\MonthlySaleReportFormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MonthlySaleReportController extends Controller
{
    private $monthlySaleReportService;

    public function __construct(MonthlySaleReportService $monthlySaleReportService)
    {
        $this->monthlySaleReportService = $monthlySaleReportService;
    }

    public function index(Request $request)
    {
        $response = $this->monthlySaleReportService->index($request);
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
