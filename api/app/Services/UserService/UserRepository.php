<?php

namespace App\Services\UserService;

use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Arr;

class UserRepository
{
    protected function transformSaleUsers(object $user): array
    {
        $transformed = [
            "id" => $user->id,
            "first_name" => $user->first_name,
            "last_name" => $user->last_name,
            "email" => $user->email,
            "organization_code" => $user->organization_code,
            "role" => optional($user->role)->role_name,
            "branch" => optional($user->branches)->name,

        ];


        return $transformed;
    }
    protected function transformUsers(object $user): array
    {
        $transformed = [
            "user_id" => $user->id,
            "first_name" => $user->first_name,
            "last_name" => $user->last_name,
            "phone_number" => $user->phone_number,
            "email" => $user->email,
            'organization_id' => $user->organization_id,
            "company_name" => optional($user->organization)->organization_name,
            "contact_person" => optional($user->organization)->contact_person,
            "company_address" => optional($user->organization)->company_address,
            // "organization_name" => optional($user->organization)->organization_name,
            "organization_code" => optional($user->organization)->organization_code,
            "organization_logo" => optional($user->organization)->organization_logo,
            "organization_type" => optional($user->organization)->organization_type,
            "role" => optional($user->role)->role_name,
            "branch" => optional($user->branches)->name,

        ];


        return $transformed;
    }
    private function returnUserDetail($user_id)
    {

        $user = User::select(
            "id",
            "first_name",
            "middle_name",
            "last_name",
            "phone_number",
            'email',
            'role_id',
            'organization_id',
            'branch_id'
        )
                           ->with('role:id,role_name', 'branches:id,name')
                              ->where('id', $user_id)
                              //->with('organization')->first();
                              ->with('organization:id,organization_name,organization_code,organization_type,organization_logo,user_id,contact_person')->first();

        //    return $user;
        if ($user) {

            return $this->transformUsers($user);
        }

        return false;
    }
    public function findById($user_id)
    {
        return $this->returnUserDetail($user_id);
    }
    public function authUser()
    {
        $user_id = Auth::user()->id;
        return $this->returnUserDetail($user_id);
    }
    public function allSupplier()
    {
        $users = User::select(
            "id",
            "first_name",
            "last_name",
            "phone_number"
        )
            ->where('type_id', 3) // Only users with type_id 3
            ->whereNotNull('email_verified_at') // Only users with verified emails
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'supplier_detail' => $user->first_name . " " . $user->last_name . " " . $user->phone_number,
                ];
            });

        if ($users) {
            return response()->json(['data' => $users], 200);
        }

        return response()->json(['data' => []], 200); // Return empty array if no users found
    }

    public function userDetail()
    {

        $users = User::select(
            "id",
            "first_name",
            "middle_name",
            "last_name",
            "phone_number",
            'email',
            'role_id',
            'organization_id',
            'branch_id'
        )
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


        $user = User::where('type_id', 0)
            ->where(function ($query) use ($searchCriteria) {
                $query->whereRaw('LOWER(email) LIKE ?', ['%' . strtolower($searchCriteria) . '%'])
                    ->orWhereRaw('LOWER(phone_number) LIKE ?', ['%' . strtolower($searchCriteria) . '%'])
                    ->orWhereRaw('LOWER(first_name) LIKE ?', ['%' . strtolower($searchCriteria) . '%'])
                    ->orWhereRaw('LOWER(last_name) LIKE ?', ['%' . strtolower($searchCriteria) . '%']);
            })
            ->get();


        return $user;


    }
    public function getUser($request)
    {

        $type = $request['type'];
        //return  $type;
        $branchId = 'all';
        if(isset($request['branch_id']) &&  auth()->user()->role->role_name == 'Admin') {
            $branchId = $request['branch_id'];
        } elseif (!in_array(auth()->user()->role->role_name, ['Admin', 'Super Admin'])) {

            $branchId = auth()->user()->branch_id;
        }
        if($type == 'supplier') {


            // Define the base query with common select and with clauses
            $baseQuery = User::select('id', 'first_name', 'last_name', 'organization_id', 'type_id', 'phone_number', 'email', 'branch_id', 'created_at')
                ->with('branches:id,name');

            // Clone the base query for type and branch conditions
            $usersByTypeAndBranch = (clone $baseQuery)
                ->where('type_id', 3);



            if ($branchId !== 'all') {
                // Apply the where clause if branch_id is not 'all' and the user is not admin
                $usersByTypeAndBranch->where('branch_id', $branchId);
            }

            // Clone the base query for email condition
            $usersByEmail = (clone $baseQuery)
                ->where('email', 'system_supplier@gmail.com');

            // Combine the results using union
            $users = $usersByTypeAndBranch->union($usersByEmail)->latest()->paginate(20);

            $users->getCollection()->transform(function ($user) {
                return $this->transformUsers($user);
            });

            return $users;



        } elseif($type == 'sales_personnel') {



            $query = User::select('id', 'first_name', 'last_name', 'email', 'role_id', 'organization_code', 'branch_id')
            ->where('type_id', 0)
            ->with('role:id,role_name', 'branches:id,name');


            if ($branchId !== 'all') {
                // Apply the where clause if branch_id is not 'all' and the user is not admin
                $query->where('branch_id', $branchId);
            }
            //->where('organization_id', Auth::user()->organization_id)
            $user = $query->latest()->paginate(20);
            $user->getCollection()->transform(function ($user) {
                return $this->transformSaleUsers($user);
            });
            return $user;


        }




    }
    public function getUserByEmail($email)
    {
        return User::where('email', $email)
           ->with([
               'role:id,role_name',
               'role.permissions:id,role_id,page_id,read,write,update,del',
               'role.permissions.page:id,page_name'
           ])
           ->first();






    }
    public function transformUser($user)
    {

        $userArray = [

            "role_name" => optional($user->role)->role_name,
            "permissions" => []
        ];

        // Adding permissions array with transformed structure
        if ($user->role && $user->role->permissions) {
            $userArray['permissions'] = $user->role->permissions->map(function ($permission) {
                return [
                    'page_name' => optional($permission->page)->page_name,
                    'read' => $permission->read,
                    'write' => $permission->write,
                    'update' => $permission->update,
                    'del' => $permission->del
                ];
            });
        }

        return $userArray;

    }
    public function getUserByToken($token)
    {

        return  User::where('token', $token)->select('id', 'first_name', 'last_name', 'organization_id', 'type_id', 'phone_number', 'email')->first();

    }

    public function getUserByEmailAndOrganizationCode(array $request)
    {

        return  User::where([['email', $request['email']], ['organization_code', $request['organization_code']]])->first();

    }
    public function authenticateUser(array $request)
    {
        return $this->getUserByEmail($request['email']);

    }

    public function createUser(array $data)
    {

        try {

            $filteredData = Arr::except($data, ['organization_type']);


            // return  $filteredData ;
            return User::updateOrCreate(
                ['email' => $filteredData['email']],
                $filteredData
            );

        } catch (QueryException $exception) {
            Log::channel('insertion_errors')->error('Error creating or updating user: ' . $exception->getMessage());
            throw new ModelNotFoundException('Insertion or update error while creating user.');
        }
    }
    public function updateUserToken(User $user, $newToken)
    {

        $user->token = $newToken;
        $user->save();

        return $user;
    }
    public function updateUserByToken($token, $newPassword)
    {

        $user = User::where('token', $token)->first();

        if (!$user) {

            return null;
        }
        $user->password = Hash::make($newPassword);
        // Save the changes to the user model
        $user->save();

        return $user;
    }
    public function updateUserById($id, $request)
    {

        $user = User::where('id', $id)->first();

        if (!$user) {

            return null;
        }

        $user->update($request);

        return $user;
    }
    public function delete($id)
    {

        try {
            $user = User::where('id', $id)->first();

            if ($user) {

                return $user->delete();
            }
            return null;
        } catch (QueryException $e) {
            Log::channel('insertion_errors')->error('Error creating or updating user: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error deleting user.',
                'errors' => 'There was an error deleting this user'
            ], 500);
        }
    }
    public function getuserOrgAndBranchDetail($user_id = null)
    {
        // dd($user_id);
        // return $user_id;
        // Use the passed $user_id, or fallback to the authenticated user's ID
        $user_id = auth()->user()->id;

        $user = User::select('organization_id', 'id', 'branch_id')
            ->where('id', $user_id)
            ->with(
                'organization:id,organization_name,organization_logo,company_address,company_phone_number,company_email',
                'branches:id,name,address,email,phone_number,state_id,country_id'
            )->first();

        // Flatten the data and remove unwanted fields
        if ($user) {
            return [

                'organization_name' => $user->organization->organization_name,
                'organization_logo' => $user->organization->organization_logo,
                'company_address' => $user->organization->company_address,
                'company_phone_number' => $user->organization->company_phone_number,
                'company_email' => $user->organization->company_email,
                'branch_name' => $user->branches->name,
                'branch_address' => $user->branches->address,
                'branch_email' => $user->branches->email,
                'branch_phone_number' => $user->branches->phone_number,
                'country_name' => $user->branches->country_name,
                'state_name' => $user->branches->state_name,
            ];
        } else {
            return [];
        }
    }

}
