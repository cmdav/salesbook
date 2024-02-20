<?php

namespace App\Http\Controllers\Supply;
use App\Http\Controllers\Controller;
use App\Http\Requests\SupplierOrganizationFormRequest;
use App\Services\Supply\SupplierOrganizationService\SupplierOrganizationService;
use App\Models\SupplierOrganization;
use Illuminate\Http\Request;

class SupplierOrganizationController extends Controller
{
    protected $supplierOrganizationService;

    public function __construct(SupplierOrganizationService $supplierOrganizationService)
    {
        $this->supplierOrganizationService = $supplierOrganizationService;
    }
    public function index()
    {
        $organization = $this->supplierOrganizationService->getAllSupplierOrganization();
        return response()->json($organization);
    }

    public function store(Request $request)
    {
        
        $organization = $this->supplierOrganizationService->createSupplierOrganization($request->all());
        return response()->json($organization, 201);
    }

    public function show($id)
    {
        $organization = $this->supplierOrganizationService->getSupplierOrganizationById($id);
        return response()->json($organization);
    }

    public function update($id, Request $request)
    {
       
        $organization = $this->supplierOrganizationService->updateSupplierOrganization($id, $request->all());
        return response()->json($organization);
    }

    public function destroy($id)
    {
        $this->supplierOrganizationService->deleteSupplierOrganization($id);
        return response()->json(null, 204);
    }
}
