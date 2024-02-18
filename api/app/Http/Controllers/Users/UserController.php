<?php

namespace App\Http\Controllers\Users;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\UserFormRequest;
use App\Services\UserService\UserService;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\JsonResponse;
use App\Mail\NewUserHasRegisterEmail;

class UserController extends Controller
{
    
    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;

    }
   
    public function store(UserFormRequest $request)
    { 	
        
           
            $user = $this->userService->createUser($request->all());
            if($user){
               
                Mail::to($user['email'])->send(new NewUserHasRegisterEmail($user));

                return response()->json(['message' => 'Verify your account using the verification link sent to your email.'], 200);

            }
            return response()->json(['message' => 'Try again.'], 500);
            
           
        
    }
   
}
