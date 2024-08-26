<?php

namespace App\Http\Controllers\SellingUnit;
use App\Http\Controllers\Controller;
use App\Services\SellingUnit\SellingUnitService\SellingUnitService;
use App\Http\Requests\SellingUnit\SellingUnitFormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SellingUnitController extends Controller
{
    private $sellingUnitService;

    public function __construct(SellingUnitService $sellingUnitService)
    {
        $this->sellingUnitService = $sellingUnitService;
    }

    public function index()
    {
        return $this->sellingUnitService->index();
    }

    public function show($id)
    {
        return $this->sellingUnitService->show($id);
    }

    public function store(SellingUnitFormRequest $request)
    {
        return $this->sellingUnitService->store($request->all());
    }

    public function update(SellingUnitFormRequest $request, $id)
    {
        return $this->sellingUnitService->update($request->all(), $id);
    }

    public function destroy($id)
    {
        return $this->sellingUnitService->destroy($id);
    }
}