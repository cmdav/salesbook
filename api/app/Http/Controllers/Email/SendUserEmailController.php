<?php

namespace App\Http\Controllers\Email;

use App\Http\Controllers\Controller;
use App\Services\UserService\UserService;
use App\Services\Email\EmailService;
use App\Services\Inventory\OrganizationService\OrganizationService;
use App\Services\Supply\SupplierOrganizationService\SupplierOrganizationService;
use App\Http\Requests\EmailFormRequest;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;


class SendUserEmailController extends Controller
{
    protected UserService $userService;

    protected EmailService $emailService;

    protected OrganizationService $organizationService;

    protected SupplierOrganizationService $supplierOrganizationService;

    protected $email;

   

    protected $organization_code;

    public function __invoke(EmailFormRequest $request,
                            UserService $userService,
                            EmailService $emailService, 
                            OrganizationService $organizationService,
                            SupplierOrganizationService $supplierOrganizationService
                        )
    {
        
        $this->userService = $userService;
        $this->emailService = $emailService; 
        $this->email = $request->email;
        $this->organizationService = $organizationService;
        $this->supplierOrganizationService = $supplierOrganizationService;
        $this->reqs = $request;
       

        if($request->type === 'resend'){

           return  $this->resendEmail();
        }
        else if($request->type === 'reset-password'){

            return  $this->passwordResetEmail();
         }
         else if($request->type === 'invitation'){

            $this->organization_code = $request->organization_code;
            return  $this->InvitationEmail($request->organization_id,$request->first_name,$request->last_name);
         }
       
      

    }
    private function resendEmail(){

        $user = $this->userService->authenticateUser($this->email);
    
        if ($user && !$user->email_verified_at) {
          
            $newToken = \Str::uuid()->toString();
    
         
            $this->userService->updateUserToken($user, $newToken);
    
            // Proceed to resend the email
            if ($this->emailService->sendEmail($user, 'resend', $newToken)) {

                $user->touch();
                return response()->json(['message' => 'Verification link resent successfully.']);

            } else {
                return response()->json(['message' => 'Network error.'], 500);
            }
        } else {
            return response()->json(['message' => 'Email already verified or user does not exist.'], 422);
        }
    }
    
    private function passwordResetEmail(){
        $user = $this->userService->authenticateUser($this->email);
    
        if ($user && $user->email_verified_at) {
           
            $newToken = \Str::uuid()->toString();
    
            
            $this->userService->updateUserToken($user, $newToken);
            //proceed to send password reset email
            if ($this->emailService->sendEmail($user, 'reset-password', $newToken)) {

                $user->touch();

                return response()->json(['message' => 'Password reset link has been sent.']);

            } else {

                return response()->json(['message' => 'Network error.'], 500);
            }
        } else {
            return response()->json(['message' => 'Email has not verified or user does not exist.'], 422);
        }
    }
    
    
    private function invitationEmail($organization_id, $first_name, $last_name){
    
        
        $organizationInfo =$this->organizationService->getOrganizationById($organization_id);

        $data=[
            'organization_name'=>$organizationInfo->organization_name,
            'organization_id'=>$organization_id
        ];
        
       
        $user = $this->userService->authenticateUser($this->email);
       
        //new user;
        if (!$user || is_null($user->email_verified_at)) {
          
            try {
                if (!$user || is_null($user->email_verified_at)) {
                    $newUser = $this->userService->createUser([
                        'email' => $this->email,
                        'type_id' => 1,
                        'first_name' => $first_name,
                        'last_name' => $last_name,
                        'password' => 'none',
                        'organization_id' => $organization_id
                    ]);
            
                    $this->supplierOrganizationService->createSupplierOrganization([
                        'supplier_id' => $newUser->id,
                        'organization_id' => $organization_id,
                    ]);
            
                    if ($this->emailService->sendEmail($newUser, "new-supplier", $data)) {
                        return response()->json(['message' => 'Invitation email has been sent to this supplier.']);
                    } else {
                        return response()->json(['message' => 'Network error.'], 500);
                    }
                }
            } catch (\Exception $e) {
                return response()->json(['message' => $e->getMessage()], 500);
            }
            
        }// existing user
        else{
                $this->supplierOrganizationService->createSupplierOrganization([

                    'supplier_id' =>$user->id,
                    'organization_id'=>$organization_id,
                
                ]);
            
                if ($this->emailService->sendEmail($user, 'old-supplier',  $data)) {

            
                    return response()->json(['message' => 'Invitation email has been sent to this supplier.']);
        
                } else {
                
                    return response()->json(['message' => 'Network error.'], 500);
                }
        }
        
    
    }
        

}
