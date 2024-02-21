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
            $request->validate([
                'password' => [
                    'required',
                    'string',
                    'min:8',
                    'max:30',
                    'confirmed',
                    'regex:/[a-z]/',      // must contain at least one lowercase letter
                    'regex:/[A-Z]/',      // must contain at least one uppercase letter
                    'regex:/[0-9]/',      // must contain at least one digit
                    'regex:/[@$!%*#?&]/', // must contain a special character
                ]
            ]);
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
