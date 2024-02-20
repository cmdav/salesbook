<?php

namespace App\Http\Controllers\Supply;
use App\Http\Controllers\Controller;
use App\Http\Requests\SupplierOrganizationFormRequest;
use App\Services\Supply\OrganizationService\OrganizationService;
use App\Models\SupplierOrganization;
use Illuminate\Http\Request;

class SupplierOrganizationController extends Controller
{
    protected $organizationService;

    public function __construct(OrganizationService $organizationService)
    {
        $this->organizationService = $organizationService;
    }
    public function index()
    {
        $organization = $this->organizationService->getAllOrganization();
        return response()->json($organization);
    }

    public function store(Request $request)
    {
        $organization = $this->organizationService->createOrganization($request->all());
        return response()->json($organization, 201);
    }

    public function show($id)
    {
        $organization = $this->organizationService->getOrganizationById($id);
        return response()->json($organization);
    }

    public function update($id, Request $request)
    {
       
        $organization = $this->organizationService->updateOrganization($id, $request->all());
        return response()->json($organization);
    }

    public function destroy($id)
    {
        $this->organizationService->deleteOrganization($id);
        return response()->json(null, 204);
    }
}
