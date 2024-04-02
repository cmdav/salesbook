<?php

namespace App\Http\Controllers\Security;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Security\JobRoleServices\JobRoleService;


class AllJobRoleController extends Controller
{
    
    protected JobRoleService $JobRoleService;

    public function __invoke(JobRoleService $JobRoleService)
    {
        
        $this->JobRoleService = $JobRoleService;
        if($JobRole =$this->JobRoleService->names()){
            return response()->json($JobRole);
        }
        return response()->json(['message'=>'JobRole not found'], 404);
        
         

    }

   
  
   
}
