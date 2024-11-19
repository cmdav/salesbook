<?php

namespace App\Http\Controllers\Products;
use App\Http\Controllers\Controller;
use App\Services\Products\MeasurementGroupService\MeasurementGroupService;
use App\Http\Requests\Products\MeasurementGroupFormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MeasurementGroupController extends Controller
{
    private $measurementGroupService;

    public function __construct(MeasurementGroupService $measurementGroupService)
    {
        $this->measurementGroupService = $measurementGroupService;
    }

    public function index()
    {
        return $this->measurementGroupService->index();
    }

    public function show($id)
    {
        return $this->measurementGroupService->show($id);
    }

    public function store(MeasurementGroupFormRequest $request)
    {
        return $this->measurementGroupService->store($request->all());
    }

    public function update(MeasurementGroupFormRequest $request, $id)
    {
        return $this->measurementGroupService->update($request->all(), $id);
    }

    public function destroy($id)
    {
        return $this->measurementGroupService->destroy($id);
    }
}