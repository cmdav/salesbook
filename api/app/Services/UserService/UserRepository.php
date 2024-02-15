<?php

namespace App\Services\UserService;
use App\Models\User;
use Illuminate\Database\QueryException;



class UserRepository
{
   
    public function getUserByEmail($email){

        return  User::where('email', $email)->first();

    }
    public function authenticateUser(array $request)
    {
        return $this->getUserByEmail($request['email']);
        
    }

    public function createUser(array $data)
    {
        try {
      
             return User::Create($data);
             
        } catch (QueryException $exception) {

            return response()->json(['message' => 'Insertion failed.'], 500);
        }
    }

   
}
