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
    public function index(Request $request){

        $validatedData = $request->validate([
            'type' => 'required|in:supplier,customer'
        ]);

       return  $this->userService->getUser($validatedData['type']);
    }
    public function show($id){

        if($user =$this->userService->findById($id)){
            return response()->json($user);
        }
        return response()->json(['message'=>'user not found'], 404);
    }
    public function store(UserFormRequest $request)
    { 	
       $response ='Registration successful.';
          
        DB::beginTransaction(); 

        try {
          
            $user = $this->userService->createUser($request->all());
           
        
            if (!$user) {

                return response()->json(['message' => 'User creation failed.'], 500);
            }
            //1 registration email
            if($request->type_id == 2){
                $this->emailService->sendEmail($user, 'register', $user->token);
                $response ='Verify your account using the verification link sent to your email.';
            }
    
        
            DB::commit(); 
        
            return response()->json(['message' => $response], 200);
            
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
