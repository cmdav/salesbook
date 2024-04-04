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
        $transformed = [
            "user_id" => $user->id,
            "first_name" => $user->first_name,
            "last_name" => $user->last_name,
            "phone_number" => $user->phone_number,
            "email" => $user->email,
			 "organization_id" => $user->organization_id,
        ];
    
        // Add supplier information only if type_id is 1
        if ($user->type_id == 1) {
            $transformed += [
                "bank_name" => optional($user->supplier)->bank_name,
                "account_name" => optional($user->supplier)->account_name,
                "account_number" => optional($user->supplier)->account_number,
                "state" => optional($user->supplier)->state,
                "address" => optional($user->supplier)->address,
            ];
        }
    
        return $transformed;
    }
    private function returnUserDetail($user_id){
        $query = User::select('id', 'first_name', 'last_name', 'organization_id', 'type_id', 'phone_number', 'email')
             ->where('id', $user_id);
            $user = $query->when(User::where('id', $user_id)->value('type_id') == 1, function ($query) {
            return $query->with(['supplier:id,user_id,bank_name,account_name,account_number,state,address']);
            })->first();

            if ($user) {
            return $this->transformUsers($user);
            }

            return false;
    }
    public function findById($user_id){
        return $this->returnUserDetail($user_id);
    }
    public function authUser(){
        $user_id =Auth::user()->id;
        return $this->returnUserDetail($user_id);
    }
    public function userDetail(){

        $users = User::select("id", "first_name", "last_name", "phone_number")
             ->where('type_id', 0)
             ->get()
             ->map(function ($user) {
                 return [
                     'id' => $user->id,
                     'customer_id' =>  $user->first_name." " .$user->last_name." ". $user->phone_number,
                     
                 ];
             });

        return $users;

         
    }
    public function searchUser($searchCriteria)
    {
       
       
            $user = User::where(function($query) use ($searchCriteria) {
                $query->where('email', 'like', '%' . $searchCriteria . '%')
                  ->orWhere('phone_number', 'like', '%' . $searchCriteria . '%');
            })->first(); 

           
            if ($user) {
           
                if ($user->type_id == 1) {
                   
                    $user->load(['supplier' => function($query) {
                        $query->select('id', 'user_id', 'bank_name', 'account_name', 'account_number', 'state', 'address');
                    }]);
                }
                return $this->transformUsers($user);
            }

            
            return false;

      
    }
    public function getUser($type){

        if($type == 'supplier')
        {
                        $user = User::select('id', 'first_name', 'last_name', 'organization_id', 'type_id', 'phone_number', 'email')
                        ->where('type_id', 1) 
                        ->where('organization_id', Auth::user()->organization_id)
                        ->with(['supplier:id,user_id,bank_name,account_name,account_number,state,address'])
                        ->whereHas('supplierOrganization', function($query) {
                            $query->where('organization_id', Auth::user()->organization_id); 
                        })
                        ->latest()->paginate(3);
                         $user->getCollection()->transform(function ($user) {
                                    return $this->transformUsers($user);
                        });
                        return $user;
            

        }else{
            if($type == 'company_customer'){
                
                return  User::select('id','company_name','contact_person','type_id','phone_number','email')
                ->where('role_id', 1)
                ->where('organization_id', Auth::user()->organization_id)
                ->latest()->paginate(3);
            }
             return  User::select('id','first_name','last_name','organization_id','type_id','phone_number','email','company_name','contact_person','company_address')
                                ->where('type_id', 0)
                                ->where('role_id', 0)
                                ->where('organization_id', Auth::user()->organization_id)
                                ->latest()->paginate(3);
        }

        

    }
    public function getUserByEmail($email){

        return  User::where('email', $email)->first();

    }
    public function getUserByToken($token){

        return  User::where('token', $token)->select('id','first_name','last_name','organization_id','type_id','phone_number','email')->first();

    }
    
    public function getUserByEmailAndOrganizationCode(array $request){

        return  User::where([['email', $request['email']], ['organization_code', $request['organization_code']]])->first();

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
    public function updateUserById($id, $request){
    
        $user = User::where('id', $id)->first();

        if (!$user) {

            return null;
        }
        $user->update($request);
    
        return $user; 
    }
    

   
}
