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
    public function searchUser($searchCriteria)
    {
        return $this->userRepository->searchUser($searchCriteria); 
    }
    public function userDetail()
    {
        return $this->userRepository->userDetail(); 
    }
    public function getUser($type)
    {

        if($type === 'profile'){
            return $this->userRepository->authUser();
        }
        return $this->userRepository->getUser($type);
    }
   
    public function findById($id)
    {
        return $this->userRepository->findById($id);
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
    public function  updateUserByToken($token, $newPassword)
    {
        return $this->userRepository->updateUserByToken($token, $newPassword);
    }
    public function  updateUserById($id, $request)
    {
        return $this->userRepository->updateUserById($id, $request);
    }

    
   
   
}