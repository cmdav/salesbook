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
           // 'type'=>'required'
            'type' => 'required|in:supplier,customer,profile,company_customer,company,sales_personnel'
        ]);

       return  $this->userService->getUser($validatedData['type']);
    }
    public function show($id){

        if($user =$this->userService->findById($id)){
            return response()->json($user);
        }
        return response()->json(['message'=>'user not found'], 404);
    }
   // public function store(Request $request)
    	
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
            if (!$request->has('role_id')) {
               
               //dd($user->organization_code);
                $this->emailService->sendEmail($user, 'register', $user->organization_code);
                $response ='Verify your account using the verification link sent to your email.';
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
    public function update($id, Request $request){

        $user = $this->userService->updateUserById($id, $request->all());
        if($user){
            return response()->json(['message' => 'Update successful .'], 200);
        }else{
            return response()->json(['message' => 'An error occur. Please try again .'], 500);
        }

    }
    public function destroy($id)
    {
        $this->userService->deleteUser($id);
        return response()->json(null, 204);
    }
   
}
