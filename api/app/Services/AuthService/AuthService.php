<?php

namespace App\Services\AuthService;
use App\Services\UserService\UserRepository;
use App\Services\Security\SubscriptionStatusService\SubscriptionStatusRepository;
use Illuminate\Support\Facades\Hash;


class AuthService
{
    protected UserRepository $userRepository;
    protected SubscriptionStatusRepository $subscriptionStatusRepository;
    

    public function __construct(UserRepository $userRepository, SubscriptionStatusRepository $subscriptionStatusRepository)
    {
        $this->userRepository = $userRepository;
        $this->subscriptionStatusRepository = $subscriptionStatusRepository;

    }

    private function passwordValidation($inputPassword, $savedPassword){

        return Hash::check($inputPassword, $savedPassword);     
    }
    
    public function authenticateUser(array $request)
    {
       // get user detail using their email
        $user = $this->userRepository->authenticateUser($request);
       
      
        if (!$user) {
            
            return response()->json(['message' => 'Invalid credential'], 401);
        }
        

        if (is_null($user->email_verified_at)) {
            
            return response()->json(['message' => 'Your email is not verified.'], 401);
        }
        
        if(!$this->subscriptionStatusRepository->checkSubscriptionStatus($user->organization_id)){

            return response()->json(['message' => 'Your subscription has expired.'], 401);
        }
       
        if($this->passwordValidation($request['password'], $user->password)){

            return [
                'token' => $user->createToken('api-token')->plainTextToken,
                'user' => [
                    'type' => $user->type_id, 
                    'organization_id'=>$user->organization_id,
                    'organization_name'=>$user->company_name,
                    'role_id'=>$user->role_id,
                    'permission' => $this->userRepository->transformUser($user),
                ],
                'message' => "Success", 
                'status' => '200', 
            ];

        }else{

            return response()->json(['message' => 'Incorrect credentials'], 401);
        }
       
    }
    
    
   
   
}