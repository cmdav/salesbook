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
       
        $user = $this->userRepository->authenticateUser($request);
        return $this->passwordValidation($request['password'], $user->password) ? [$user->createToken('api-token')->plainTextToken, $user]: [];
       
    }
    
    
   
   
}