<?php

namespace App\Http\Controllers\SellingUnit;
use App\Http\Controllers\Controller;
use App\Services\SellingUnit\PurchaseUnitService\PurchaseUnitService;
use App\Http\Requests\SellingUnit\PurchaseUnitFormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PurchaseUnitController extends Controller
{
    private $purchaseUnitService;

    public function __construct(PurchaseUnitService $purchaseUnitService)
    {
        $this->purchaseUnitService = $purchaseUnitService;
    }

    public function index()
    {
        return $this->purchaseUnitService->index();
    }

    public function show($id)
    {
        return $this->purchaseUnitService->show($id);
    }

    public function store(PurchaseUnitFormRequest $request)
    {
        return $this->purchaseUnitService->store($request->all());
    }

    public function update(PurchaseUnitFormRequest $request, $id)
    {
        return $this->purchaseUnitService->update($request->all(), $id);
    }

    public function destroy($id)
    {
        return $this->purchaseUnitService->destroy($id);
    }
}