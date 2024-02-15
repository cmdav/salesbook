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
    
    
   
   
}