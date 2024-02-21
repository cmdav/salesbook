<?php

namespace App\Services\UserService;
use App\Services\UserService\UserRepository;


class UserService
{
    protected UserRepository $userRepository;
    

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;

    }

    
    public function createUser(array $all)
    {
    
        return $this->userRepository->createUser($all);
       
    }
    public function authenticateUser($email)
    {
       
        return $this->userRepository->getUserByEmail($email);
        
    
    }
    public function getUserByToken($token)
    {
       
        return $this->userRepository->getUserByToken($token);
        
    
    }
    public function updateUserToken(\App\Models\User $user, $newToken)
    {
        return $this->userRepository->updateUserToken($user, $newToken);
    }

    
   
   
}