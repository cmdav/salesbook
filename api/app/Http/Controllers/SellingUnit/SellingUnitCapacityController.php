<?php

namespace App\Http\Controllers\SellingUnit;
use App\Http\Controllers\Controller;
use App\Services\SellingUnit\SellingUnitCapacityService\SellingUnitCapacityService;
use App\Http\Requests\SellingUnit\SellingUnitCapacityFormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SellingUnitCapacityController extends Controller
{
    private $sellingUnitCapacityService;

    public function __construct(SellingUnitCapacityService $sellingUnitCapacityService)
    {
        $this->sellingUnitCapacityService = $sellingUnitCapacityService;
    }

    public function index()
    {
        return $this->sellingUnitCapacityService->index();
    }

    public function show($id)
    {
        return $this->sellingUnitCapacityService->show($id);
    }

    public function store(SellingUnitCapacityFormRequest $request)
    {
        return $this->sellingUnitCapacityService->store($request->all());
    }

    public function update(SellingUnitCapacityFormRequest $request, $id)
    {
        return $this->sellingUnitCapacityService->update($request->all(), $id);
    }

    public function destroy($id)
    {
        return $this->sellingUnitCapacityService->destroy($id);
    }
}