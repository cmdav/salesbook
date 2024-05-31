<?php
namespace App\Http\Controllers\Security;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Security\PermissionServices\PermissionService;

class PermissionController extends Controller
{

    protected $PermissionService;

    public function __construct(PermissionService $PermissionService)
    {
       $this->PermissionService = $PermissionService;
    }
    public function index(Request $request)
    {
        //return $request;
        $validatedData = $request->validate([
            'role' => 'required|exists:job_roles,id'
        ]);
       
        $Page =$this->PermissionService->index($validatedData['role']);
        return response()->json($Page);
    }

    public function store(Request $request)
    {
        $request->validate([
            'role_id' => 'required|exists:job_roles,id',
            'permissions' => 'required|array',
            'permissions.*.page_id' => 'required|exists:pages,id',
        ]);
        return $this->PermissionService->create($request->all());
        
    }

    public function show($id)
    {
        $Page =$this->PermissionService->show($id);
        return response()->json($Page);
    }
  
    public function update($id, Request $request)
    {
       
        $Page =$this->PermissionService->update($id, $request->all());
        return response()->json($Page);
    }

    public function destroy($id)
    {
       $this->PermissionService->delete($id);
        return response()->json(null, 204);
    }
}
