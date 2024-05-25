<?php

namespace App\Http\Controllers\Users;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\UserService\UserService;
use Illuminate\Http\JsonResponse;



class AllSupplierController extends Controller
{
    
    protected UserService $userService;

    public function __invoke(UserService $userService)
    {
        
        $this->userService = $userService;
        if($user =$this->userService->allSupplier()){
            return $user;
        }
        return response()->json(['message'=>'user not found'], 404);
        
         

    }

   
  
   
}
