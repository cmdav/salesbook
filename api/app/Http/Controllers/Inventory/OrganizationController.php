<?php

namespace App\Http\Controllers\Inventory;
use App\Http\Controllers\Controller;
use App\Services\Inventory\OrganizationService\OrganizationService;

use App\Http\Requests\OrganizationFormRequest;
use Illuminate\Support\Facades\Storage;

class OrganizationController extends Controller
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

    public function store(OrganizationFormRequest $request)
    {
        $data = $request->all();

        if ($request->hasFile('organization_logo')) {
            $data['organization_logo'] = $this->uploadOrganizationLogo($request->file('organization_logo'));
        }

        $organization = $this->organizationService->createOrganization($data);
        return response()->json($organization, 201);
    }
    private function uploadOrganizationLogo($file): string
    {
        $filename = time() . '.' . $file->getClientOriginalExtension(); // Create a unique filename
        $path = $file->storeAs('public/organizations', $filename); // Store the file in the storage/app/public/organizations directory
        return Storage::url($path); // Return the URL to access the uploaded file
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
