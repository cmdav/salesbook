<?php

namespace App\Http\Controllers\Products;

use App\Http\Controllers\Controller;
use App\Services\Products\SearchMeasurementGroupService\SearchMeasurementGroupService;
use App\Http\Requests\Products\SearchMeasurementGroupFormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SearchMeasurementGroupController extends Controller
{
    private $searchMeasurementGroupService;

    public function __construct(SearchMeasurementGroupService $searchMeasurementGroupService)
    {
        $this->searchMeasurementGroupService = $searchMeasurementGroupService;
    }

    public function index(Request $request)
    {
        $data = $this->searchMeasurementGroupService->index($request['search']);
        if (!$data->isEmpty()) {
            return response()->json($data, 200);
        }
        return response()->json(['success' => false, 'message' => 'No record found'], 404);
    }
}
