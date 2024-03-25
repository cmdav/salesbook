<?php

namespace App\Http\Controllers\Inventory;
use App\Http\Controllers\Controller;
use App\Services\Inventory\MeasurementService\MeasurementService;
use Illuminate\Http\Request;

class SearchMeasurementController extends Controller
{
    protected $measurementService;

    public function __construct(MeasurementService $measurementService)
    {
       $this->measurementService = $measurementService;
    }
   
    public function store(MeasurementFormRequest $request)
    {
        $measurement =$this->measurementService->createMeasurement($request->all());
        return response()->json($measurement, 201);
    }

    public function show($id)
    {
        $measurement =$this->measurementService->SearchMeasurement($id);
        return response()->json($measurement);
    }

   
}
