<?php

namespace App\Services\UserService;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;



class UserRepository
{
   
    public function getUserByEmail($email){

        return  User::where('email', $email)->first();

    }
    public function getUserByToken($token){

        return  User::where('token', $token)->first();

    }
    public function getUserByEmailAndOrganizationCode(array $request){

        return  User::where([
                             ['email', $request['email']], ['organization_code', $request['organization_code']]
                            ])->first();

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

            throw new ModelNotFoundException('The provided organization code does not exist.');
        }
    }
    public function updateUserToken(User $user, $newToken){
        // Update the user's token
        $user->token = $newToken;
        $user->save(); // Persist the changes to the database
    
        return $user; // Return the updated user
    }
   
}
