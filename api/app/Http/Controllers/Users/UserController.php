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

class UserController extends Controller
{
    
    protected UserService $userService;

    protected EmailService $emailService;

    public function __construct(UserService $userService, EmailService $emailService)
    {
        $this->userService = $userService;

        $this->emailService = $emailService; 
    }
   
    public function store(UserFormRequest $request)
    { 	
      
          
        DB::beginTransaction(); 

        try {
          
            $user = $this->userService->createUser($request->all());
           
        
            if (!$user) {

                return response()->json(['message' => 'User creation failed.'], 500);
            }
            //1 registration email
             $this->emailService->sendEmail($user, 'register');
    
        
            DB::commit(); 
        
            return response()->json(['message' => 'Verify your account using the verification link sent to your email.'], 200);
            
        }catch (ModelNotFoundException $e) {

            DB::rollBack();

            return response()->json(['message' => 'Validation Error.','errors' => ['organization_code' => ['Invalid organization code provided.']]], 422); // 422 Unprocessable Entity

        } catch (Exception $e) {

            DB::rollBack(); 

            Log::channel('email_errors')->error('Error sending email: ' . $e->getMessage());

            return response()->json(['message' => 'An error occur. Please try again .'], 500);
        }
            
           
        
    }
   
}
