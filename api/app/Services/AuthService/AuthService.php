<?php

namespace App\Services\AuthService;
use App\Services\UserService\UserRepository;
use Illuminate\Support\Facades\Hash;


class AuthService
{
    protected UserRepository $userRepository;
    

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;

    }

    private function passwordValidation($inputPassword, $savedPassword){

        return Hash::check($inputPassword, $savedPassword);     
    }
    
    public function authenticateUser(array $request)
    {
       // get user detail using their email
        $user = $this->userRepository->authenticateUser($request);

        if (is_null($user->email_verified_at)) {
            
            return response()->json(['message' => 'Your email is not verified.'], 401);
        }
        

        return $this->passwordValidation($request['password'], $user->password) ? [$user->createToken('api-token')->plainTextToken, $user]: [];
       
    }
    
    
   
   
}