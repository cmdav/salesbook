<?php

namespace App\Http\Controllers\Products;

use App\Http\Controllers\Controller;
use App\Services\Products\TotalSaleReportService\TotalSaleReportService;
use App\Http\Requests\Products\TotalSaleReportFormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TotalSaleReportController extends Controller
{
    private $totalSaleReportService;

    public function __construct(TotalSaleReportService $totalSaleReportService)
    {
        $this->totalSaleReportService = $totalSaleReportService;
    }

    public function index(Request $request)
    {
        $data = $this->totalSaleReportService->index($request);
        if (!$data->isEmpty()) {
            return response()->json(['success' => true, 'message' => 'Record retrieved successfully', 'data' => $data], 200);
        }
        return response()->json(['success' => false, 'message' => 'No record found'], 404);
    }
}
