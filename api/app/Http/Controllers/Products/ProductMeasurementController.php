<?php

namespace App\Http\Controllers\Products;
use App\Http\Controllers\Controller;
use App\Services\Products\ProductMeasurementService\ProductMeasurementService;
use App\Http\Requests\Products\ProductMeasurementFormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProductMeasurementController extends Controller
{
    private $productMeasurementService;

    public function __construct(ProductMeasurementService $productMeasurementService)
    {
        $this->productMeasurementService = $productMeasurementService;
    }

    public function index()
    {
        return $this->productMeasurementService->index();
    }

    public function show($id)
    {
        return $this->productMeasurementService->show($id);
    }

    public function store(ProductMeasurementFormRequest $request)
    {
        return $this->productMeasurementService->store($request->all());
    }

    public function update(ProductMeasurementFormRequest $request, $id)
    {
        return $this->productMeasurementService->update($request->all(), $id);
    }

    public function destroy($id)
    {
        return $this->productMeasurementService->destroy($id);
    }
}