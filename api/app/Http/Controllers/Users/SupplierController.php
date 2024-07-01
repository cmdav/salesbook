<?php

namespace App\Http\Controllers\Users;
use App\Http\Controllers\Controller;
use App\Services\Supply\SupplierService\SupplierService;
use App\Http\Requests\SupplierFormRequest;
use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    protected $supplierService;

    public function __construct(SupplierService $supplierService)
    {
        $this->supplierService = $supplierService;
    }
    public function index(Request $request)
    {
        $supplier = $this->supplierService->getAllSupplier($request);
        return response()->json($supplier);
    }

    public function store(SupplierFormRequest $request)
    {
        $supplier = $this->supplierService->createSupplier($request->all());
        return response()->json($supplier, 201);
    }

    public function show($id)
    {
        $supplier = $this->supplierService->getSupplierById($id);
        return response()->json($supplier);
    }

    public function update($id, Request $request)
    {
       
        $supplier = $this->supplierService->updateSupplier($id, $request->all());
        return response()->json($supplier);
    }

    public function destroy($id)
    {
        $this->supplierService->deleteSupplier($id);
        return response()->json(null, 204);
    }
}
