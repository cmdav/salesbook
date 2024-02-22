<?php

namespace App\Services\UserService;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth; 




class UserRepository
{
    protected function transformUsers(object $user): array
    {
        return [
            "user_id" => $user->id,
            "first_name" =>  $user->first_name,
            "last_name" =>   $user->last_name,
            "phone_number" =>  $user->phone_number,
            "email" =>  $user->email,
           "bank_name" => optional($user->supplier)->bank_name ?? null,
            "account_name" => optional($user->supplier)->account_name ?? null,
            "account_number" => optional($user->supplier)->account_number ?? null,
            "state" => optional($user->supplier)->state ?? null,
            "address" => optional($user->supplier)->address ?? null,
            //"created_at" => $user->created_at->format('d-m-y H:i:s'),
        ];
    }
    public function findById($user_id){

        $user = User::select('id', 'first_name', 'last_name', 'organization_id', 'type_id', 'phone_number', 'email')
        ->where('type_id', 1) 
        ->where('id',  $user_id)
        ->with(['supplier:id,user_id,bank_name,account_name,account_number,state,address'])
        ->first();
        if ($user) {
            return $this->transformUsers($user);
        }
        return null;
    }
    public function getUser($type){

        if($type == 'supplier')
        {
                        $user = User::select('id', 'first_name', 'last_name', 'organization_id', 'type_id', 'phone_number', 'email')
                        ->where('type_id', 1) 
                        ->where('organization_id', Auth::user()->organization_id)
                        ->with(['supplier:id,user_id,bank_name,account_name,account_number,state,address'])
                        ->whereHas('supplierOrganization', function($query) {
                            $query->where('organization_id', Auth::user()->organization_id); // Further ensure that the supplier organization matches
                        })
                        ->paginate(20);
                         $user->getCollection()->transform(function ($user) {
                                    return $this->transformUsers($user);
                        });
                        return $user;
            

        }else{
             return  User::select('id','first_name','last_name','organization_id','type_id','phone_number','email')
                                ->where('type_id', 0)->paginate(20);
        }

        

    }
    public function getUserByEmail($email){

        return  User::where('email', $email)->first();

    }
    public function getUserByToken($token){

        return  User::where('token', $token)->select('id','first_name','last_name','organization_id','type_id','phone_number','email')->first();

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
          
            return User::updateOrCreate(
                ['email' => $data['email']], 
                $data 
            );
        } catch (QueryException $exception) {
            Log::channel('insertion_errors')->error('Error creating or updating user: ' . $exception->getMessage());
            throw new ModelNotFoundException('Insertion or update error.');
        }
    }
    public function updateUserToken(User $user, $newToken){
    
        $user->token = $newToken;
        $user->save(); 
    
        return $user; 
    }
    public function updateUserByToken($token, $newPassword){
    
        $user = User::where('token', $token)->first();

        if (!$user) {

            return null;
        }
        $user->password = Hash::make($newPassword); 
        // Save the changes to the user model
        $user->save(); 
    
        return $user; 
    }
    

   
}
