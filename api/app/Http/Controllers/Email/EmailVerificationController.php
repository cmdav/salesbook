<?php

namespace App\Http\Controllers\Email;
use App\Services\Supply\SupplierOrganizationService\SupplierOrganizationService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\UserService\UserService;
use App\Services\EncryptDecryptService;
use App\Services\ExpiryService;
use Carbon\Carbon;


class EmailVerificationController extends Controller
{
    protected UserService $userService;
    protected SupplierOrganizationService $supplierOrganizationService;
    
    public function __construct(UserService $userService, SupplierOrganizationService $supplierOrganizationService)
    {
        $this->userService = $userService;
        $this->supplierOrganizationService = $supplierOrganizationService;
    }

    
    public function __invoke($hash, Request $request)
    {
        $token = EncryptDecryptService::decryptvalue($hash);
        if(!$token){
            return response()->json(['message' => 'token not found '], 404);
        }
       
        $type = $token['type'];
        if ($type == "reset-password") {
            $messages = [
                'password.required' => 'A password is required.',
                'password.string' => 'The password must be a string.',
                'password.min' => 'The password must be at least 8 characters.',
                'password.max' => 'The password may not be greater than 30 characters.',
                'password.confirmed' => 'The password confirmation does not match.',
                'password.regex' => 'The password must contain at least one lowercase letter, one uppercase letter, one digit, and one special character (@$!%*#?&).',
            ];
    
            $validator = \Validator::make($request->all(), [
                'password' => [
                    'required',
                    'string',
                    'min:8',
                    'max:30',
                    'confirmed',
                    'regex:/[a-z]/',
                    'regex:/[A-Z]/',
                    'regex:/[0-9]/',
                    'regex:/[@$!%*#?&]/',
                ]
            ], $messages);
    
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
        }
        
        
          
        $user = $this->userService->getUserByToken( $token['token']);
        
        if (!$user) {

            return response()->json(['message' => 'Invalid hash '], 404);
        }
       

        if(ExpiryService::hasLinkExpiry($user->updated_at)){

            return response()->json(['message' => 'Verification link has expired'], 401);
        }
        $user->email_verified_at = Carbon::now();
        $user->save();
      
        if($type == "reset-password"){


          if($this->userService->updateUserByToken($token['token'], $request->password)){

            return response()->json(['message' => 'Password update successfully.']);
          }
          return response()->json(['message' => 'Try again.'], 500);
        }
        else if ($type == "new-supplier") {

            if($data =$this->userService->getUserByToken($token['token'])){

                return response()->json($data);

              }
              return response()->json(['message' => 'Try again.'], 500);

        }
        else if ($type == "old-supplier") {
           

            if($this->supplierOrganizationService->updateSupplierStatus($token['otherDetail']['organization_id'], $user->id))
            {

                return response()->json(['message' => 'This action was completed successfully.']);
            }
              return response()->json(['message' => 'User information was not found.'], 404);

        }

        return response()->json(['message' => 'Email verified successfully.']);
    }
}
 