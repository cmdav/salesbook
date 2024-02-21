<?php

namespace App\Http\Controllers\Email;

use App\Http\Controllers\Controller;
use App\Services\UserService\UserService;
use App\Services\Email\EmailService;
use App\Services\Inventory\OrganizationService\OrganizationService;
use App\Http\Requests\EmailFormRequest;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;


class SendUserEmailController extends Controller
{
    protected UserService $userService;

    protected EmailService $emailService;

    protected OrganizationService $organizationService;

    protected $email;

   

    protected $organization_code;

    public function __invoke(EmailFormRequest $request,UserService $userService, EmailService $emailService, OrganizationService $organizationService)
    {
        
        $this->userService = $userService;
        $this->emailService = $emailService; 
        $this->email = $request->email;
        $this->organizationService = $organizationService;
        $this->reqs = $request;
       

        if($request->type === 'resend'){

           return  $this->resendEmail();
        }
        else if($request->type === 'reset-password'){

            return  $this->passwordResetEmail();
         }
         else if($request->type === 'invitation'){

            $this->organization_code = $request->organization_code;
            return  $this->InvitationEmail($request->organization_id);
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
    
    
    private function invitationEmail($organization_id){
    
        
        $organizationInfo =$this->organizationService->getOrganizationById($organization_id);

        $data=[
            'organization_name'=>$organizationInfo->organization_name,
            'organization_id'=>$organizationInfo->organization_id
        ];
        

        $user = $this->userService->authenticateUser($this->email);
       
      
        if(!$user){
           // proceed
            if ($this->emailService->sendEmail($this->email, "new-supplier",  $data )) {

          
                return response()->json(['message' => 'Invitation email has been sent to this supplier.']);
    
            } else {
            
                return response()->json(['message' => 'Network error.'], 500);
            }
        }// existing user
        else{
           
            if ($this->emailService->sendEmail($user, 'old-supplier',  $data)) {

          
                return response()->json(['message' => 'Invitation email has been sent to this supplier.']);
    
            } else {
            
                return response()->json(['message' => 'Network error.'], 500);
            }
        }
        
    
    }
        

}
