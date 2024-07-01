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
use Illuminate\Support\Facades\Auth;
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
       
            $request->validate([
                'password' => [
                    'required',
                    'string',
                    'min:8',
                    'max:30',
                    'confirmed',
                    'regex:/[a-z]/',      // must contain at least one lowercase letter
                    'regex:/[A-Z]/',      // must contain at least one uppercase letter
                    'regex:/[0-9]/',      // must contain at least one digit
                    'regex:/[@$!%*#?&]/', // must contain a special character
                ],
                'first_name' => 'required|string|max:55',
                'last_name' => 'required|string|max:55',
                'branch_id' => 'required|integer',
                'email' => ['required', 'email', 'max:55'],
            ]);
            $data=$request->all();
            //$data['type']='sales_personnel';
            $data['email_verified_at']=Now();
            $data['organization_code'] =Auth::user()->organization_code;
          
            $user = $this->userService->createUser($data);
           
        
            if (!$user) {

                return response()->json(['message' => 'User creation failed.'], 500);
            }
          
    
          
          
            return response()->json(['message' => "User created successfully.", 'data' =>$user], 201);
            
      
            
           
        
    }
   
   
}
