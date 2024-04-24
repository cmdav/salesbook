<?php

namespace App\Http\Controllers\Inventory;
use App\Http\Controllers\Controller;
use App\Http\Requests\MeasurementFormRequest;
use App\Services\Inventory\MeasurementService\MeasurementService;
use App\Models\Measurement;
use Illuminate\Http\Request;

class MeasurementController extends Controller
{
    protected $measurementService;

    public function __construct(MeasurementService $measurementService)
    {
       $this->measurementService = $measurementService;
    }
    public function index()
    {
       
        $measurement =$this->measurementService->getAllMeasurement();
        return response()->json($measurement);
    }

    public function store(MeasurementFormRequest $request)
    {
        $measurement =$this->measurementService->createMeasurement($request->all());
        return response()->json($measurement, 201);
    }

    public function show($id)
    {
        $measurement =$this->measurementService->getMeasurementById($id);
        return response()->json($measurement);
    }

    public function update($id, MeasurementFormRequest $request)
    {
       
        $measurement =$this->measurementService->updateMeasurement($id, $request->all());
        return response()->json($measurement);
    }

    public function destroy($id)
    {
       $this->measurementService->deleteMeasurement($id);
        return response()->json(null, 204);
    }
}
