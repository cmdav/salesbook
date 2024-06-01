<?php

namespace App\Http\Controllers\Users;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\UserFormRequest;
use App\Services\UserService\UserService;
use App\Services\Email\EmailService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;
use Exception;

class SaleUserController extends Controller
{
    
    protected UserService $userService;

   

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;

     
    }
    
    	
    public function store(Request $request)
    { 	
       
      
        
          
            $user = $this->userService->createUser($request->all());
           
        
            if (!$user) {

                return response()->json(['message' => 'User creation failed.'], 500);
            }
          
    
          
          
            return response()->json(['message' => "User created successfully.", 'data' =>$user], 201);
            
      
            
           
        
    }
   
   
}
