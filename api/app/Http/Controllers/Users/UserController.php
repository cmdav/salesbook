<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\UserFormRequest;
use App\Services\UserService\UserService;
use App\Services\Email\EmailService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;
use App\Models\JobRole;
use App\Models\User;
use Illuminate\Support\Str;
use App\Models\Organization;
use App\Models\BusinessBranch;
use App\Models\SupplierOrganization;
use Exception;

class UserController extends Controller
{
    protected UserService $userService;

    protected EmailService $emailService;

    public function __construct(UserService $userService, EmailService $emailService)
    {
        $this->userService = $userService;

        $this->emailService = $emailService;
    }
    public function index(Request $request)
    {

        // $validatedData = $request->validate([
        //    // 'type'=>'required'
        //     'type' => 'required|in:suppliers,profile,sole_properietor,company,sales_personnel'
        // ]);

        return  $this->userService->getUser($request->all());
    }
    public function show($id)
    {

        if($user = $this->userService->findById($id)) {
            return response()->json($user);
        }
        return response()->json(['message' => 'user not found'], 404);
    }
    // public function store(Request $request)

    public function store(UserFormRequest $request)
    {
        $response = 'Registration successful.';

        DB::beginTransaction();

        try {
            $userData = $request->all();
            $this->prepareUserData($userData, $request);

            $user = $this->userService->createUser($userData);

            if (!$user) {
                return response()->json(['message' => 'User creation failed.'], 500);
            }

            // Send registration email
            if (in_array($request->input('organization_type'), ['company', 'sole_properietor'])) {
                $this->emailService->sendEmail($user, 'register', $user->organization_code);
                $response = 'Verify your account using the verification link sent to your email.';
            }

            // Create Organization if necessary
            if (in_array($request->input('organization_type'), ['company', 'sole_properietor'])) {
                $this->createOrganizationAndBranch($user, $request);
            }

            DB::commit();

            return response()->json(['message' => $response], 201);

        } catch (Exception $e) {
            DB::rollBack();
            Log::channel('insertion_errors')->error('Insertion error: ' . $e->getMessage());
            return response()->json(['message' => 'An error occurred. Please try again.'], 500);
        }
    }

    public function update($id, Request $request)
    {
        $user = $this->userService->updateUserById($id, $request->all());
        if ($user) {
            // Update associated models if necessary
            if (isset($user->organization_id)) {
                SupplierOrganization::where([
                    'supplier_id' => $user->id,
                    'organization_id' => $user->organization_id
                ])->update(['status' => 1]);
            }
            return response()->json(['message' => 'Update successful.'], 200);
        } else {
            return response()->json(['message' => 'An error occurred. Please try again.'], 500);
        }
    }


    public function destroy($id)
    {
        $this->userService->deleteUser($id);
        return response()->json(null, 204);
    }

    private function prepareUserData(&$userData, $request)
    {
        //company and individual and supplier do not have role
        if (!$request->has('role_id')) {
            //Individual or Company user
            if (!$request->has('type')) {
                do {
                    $time = time();
                    $randomNumber = rand(100000, 999999);
                    $hash = hash('sha256', $randomNumber . $time);
                    $code = hexdec(substr($hash, 0, 6)) % 1000000;
                } while (User::where('organization_code', $code)->exists());

                $userData['organization_code'] = $code;
                $userData['token'] = hexdec(substr($hash, 0, 6)) % 3000000;

                $adminRole = JobRole::where('role_name', 'Admin')->first();
                $userData['role_id'] = $adminRole->id;

                if ($request->has('organization_type') && $request->input('organization_type') == 'company') {
                    $userData['type_id'] = 2;
                } elseif ($request->has('organization_type') && $request->input('organization_type') == 'sole_properietor') {
                    $userData['type_id'] = 1;
                }
            } else {
                //Supplier
                $Role = JobRole::where('role_name', 'Supplier')->first();
                $userData['role_id'] = $Role->id;

                $userData['type_id'] = 3; // Supplier type_id
                $userData['email_verified_at'] = now();
                $time = time();
                $randomNumber = rand(100000, 999999);
                $hash = hash('sha256', $randomNumber . $time);
                $userData['token'] = hexdec(substr($hash, 0, 6)) % 3000000;
            }
        } else {
            //Sales person
            $userData['email_verified_at'] = now();
            if (Auth::check()) {
                $userData['organization_code'] = Auth::user()->organization_code;
                $userData['organization_id'] = Auth::user()->organization_id;
                $userData['branch_id'] = Auth::user()->branch_id;
            }
        }
    }

    private function createOrganizationAndBranch($user, $request)
    {
        // Create Organization

        $organization = new Organization([
            'id' => Str::uuid(),
            'organization_name' => $request->input('company_name'),
            'organization_code' => $user->organization_code,
            'organization_type' => $request->input('organization_type'),
            'organization_logo' => 'logo.png',
            'user_id' => $user->id,
            'company_address' => $request->input('company_address'),
             'contact_person' => $user->first_name . ' ' . $user->last_name.' ' .  $user->contact_person,
            'company_email' => $request->input('email'),
            'company_phone_number' => $request->input('phone_number'),

        ]);
        $organization->save();


        // Create Business Branch
        $branch = new BusinessBranch([
            'name' => $request->input('company_name'),
            'contact_person' => $user->first_name . ' ' . $user->last_name.' ' .  $user->contact_person,
            'email' => $user->email,
            'address' => $request->input('company_address'),
            'phone_number' => $user->phone_number,
        ]);
        $branch->save();

        // Update user's organization and branch id
        $user->organization_id = $organization->id;
        $user->branch_id = $branch->id;
        $user->save();

    }


}
