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
       
      
       $response ='User created successfully.';
          
        DB::beginTransaction(); 

        try {
          
            $user = $this->userService->createUser($request->all());
           
        
            if (!$user) {

                return response()->json(['message' => 'User creation failed.'], 500);
            }
          
    
          
            DB::commit(); 
        
            return response()->json(['message' => $response], 201);
            
        }catch (ModelNotFoundException $e) {

            DB::rollBack();
            Log::channel('insertion_errors')->error('Error creating or updating user: ' . $e->getMessage());
            return response()->json(['message' => 'submission Error.','errors' => ['organization' => ['An error occur while creating a user.']]], 422); 

        } catch (Exception $e) {

            DB::rollBack(); 

            Log::channel('email_errors')->error('Error sending email: ' . $e->getMessage());

            return response()->json(['message' => 'An error occur. Please try again .'], 500);
        }
            
           
        
    }
   
   
}
