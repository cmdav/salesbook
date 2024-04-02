<?php
namespace App\Http\Controllers\Security;
use App\Http\Controllers\Controller;
use App\Models\JobRole;
use Illuminate\Http\Request;
use App\Services\Security\JobRoleServices\JobRoleService;

class JobRoleController extends Controller
{

    protected $jobRoleService;

    public function __construct(jobRoleService $jobRoleService)
    {
       $this->jobRoleService = $jobRoleService;
    }
    public function index()
    {
       
        $jobRole =$this->jobRoleService->index();
        return response()->json($jobRole);
    }

    public function store(Request $request)
    {
        $request->validate([
            'role_name' => 'string|required|max:30|unique:job_roles'
        ]);
        $jobRole =$this->jobRoleService->create($request->all());
        return response()->json($jobRole, 201);
    }

    public function show($id)
    {
        $jobRole =$this->jobRoleService->show($id);
        return response()->json($jobRole);
    }
  
    public function update($id, Request $request)
    {
       
        $jobRole =$this->jobRoleService->update($id, $request->all());
        return response()->json($jobRole);
    }

    public function destroy($id)
    {
       $this->jobRoleService->delete($id);
        return response()->json(null, 204);
    }
}
