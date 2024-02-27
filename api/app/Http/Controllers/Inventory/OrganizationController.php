<?php

namespace App\Http\Controllers\Inventory;
use App\Http\Controllers\Controller;
use App\Services\Inventory\OrganizationService\OrganizationService;
use App\Services\FileUploadService;
use App\Http\Requests\OrganizationFormRequest;


class OrganizationController extends Controller
{
    protected $organizationService;
    protected $fileUploadService;

    public function __construct(OrganizationService $organizationService, FileUploadService $fileUploadService)
    {
        $this->organizationService = $organizationService;
        $this->fileUploadService = $fileUploadService;
    }
    public function index()
    {
        $organization = $this->organizationService->getAllOrganization();
        return response()->json($organization);
    }

    public function store(OrganizationFormRequest $request)
    {
        $data = $request->all();

        if ($request->hasFile('organization_logo')) {
            $data['organization_logo'] = $this->fileUploadService->uploadImage($request->file('organization_logo'),'organization');
        }

        $organization = $this->organizationService->createOrganization($data);
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
