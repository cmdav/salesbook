<?php

namespace App\Http\Controllers\Users;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\UserFormRequest;
use App\Services\UserService\UserService;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\JsonResponse;
use App\Mail\NewUserHasRegisterEmail;

use Illuminate\Support\Facades\DB;
use Exception;

class UserController extends Controller
{
    
    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;

    }
   
    public function store(UserFormRequest $request)
    { 	
      
           
        DB::beginTransaction(); 

        try {
          
            $user = $this->userService->createUser($request->all());
        
            if (!$user) {
                return response()->json(['message' => 'User creation failed.'], 500);
            }
        
            Mail::to($user['email'])->send(new NewUserHasRegisterEmail($user));
        
            DB::commit(); 
        
            return response()->json(['message' => 'Verify your account using the verification link sent to your email.'], 200);
        } catch (Exception $e) {

            DB::rollBack(); 

            return response()->json(['message' => 'Could not send verification email. Please try again .'], 500);
        }
            
           
        
    }
   
}
