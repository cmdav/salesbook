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
        return $this->measurementService->createMeasurement($request->all());
      
    }

    public function show($id)
    {
        $measurement =$this->measurementService->getMeasurementById($id);
        return response()->json($measurement);
    }

    public function update($id, MeasurementFormRequest $request)
    {
       
       return $this->measurementService->updateMeasurement($id, $request->all());
       
    }

    public function destroy($id)
    {
       return $this->measurementService->deleteMeasurement($id);
        
    }
}
