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
      
        $Page =$this->PermissionService->index($request->roleId);
        return response()->json($Page);
    }

    public function store(Request $request)
    {
        // $request->validate([
        //     'page_name' => 'string|required|max:30|unique:Permission'
        // ]);
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
