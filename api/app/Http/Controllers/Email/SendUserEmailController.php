<?php

namespace App\Http\Controllers\Email;

use App\Http\Controllers\Controller;
//use Illuminate\Http\Request;
use App\Services\UserService\UserService;
//use Illuminate\Support\Facades\Mail;
//use App\Mail\NewUserHasRegisterEmail;
//use App\Services\EncryptDecryptService;
//use App\Services\ExpiryService;
use App\Services\Email\EmailService;
use App\Http\Requests\EmailFormRequest;
use Carbon\Carbon;


class SendUserEmailController extends Controller
{
    protected UserService $userService;

    protected EmailService $emailService;

    protected $email;

    public function __invoke(EmailFormRequest $request,UserService $userService, EmailService $emailService)
    {
        
        $this->userService = $userService;
        $this->emailService = $emailService; 
        $this->email = $request->email;


        if($request->type === 'resend'){

           return  $this->resendEmail();
        }
        else if($request->type === 'reset-password'){

            return  $this->passwordResetEmail();
         }
         else if($request->type === 'invitation'){

            return  $this->InvitationEmail();
         }
       
      

    }
    private function resendEmail(){
        
            
            $user = $this->userService->authenticateUser($this->email);
        
            if ($user->email_verified_at) {
                return response()->json(['message' => 'Email already verified.'], 422);
            }
        
            if ($this->emailService->sendEmail($user, 2)) {

                $user->touch();
                return response()->json(['message' => 'Verification link resent successfully.']);

            } else {
            
                return response()->json(['message' => 'Network error.'], 500);
            }
        
    }
    private function passwordResetEmail(){
        
           
        $user = $this->userService->authenticateUser($this->email);
    
        if ($this->emailService->sendEmail($user, 2)) {

            $user->touch();
            return response()->json(['message' => 'Password reset link has been sent to you.']);

        } else {
        
            return response()->json(['message' => 'Network error.'], 500);
        }
    
    }
    private function invitationEmail(){
        
        //check if the user exist   
        $user = $this->userService->authenticateUser($this->email);
    
        if ($this->emailService->sendEmail($user, 2)) {

            $user->touch();
            return response()->json(['message' => 'Password reset link has been sent to you.']);

        } else {
        
            return response()->json(['message' => 'Network error.'], 500);
        }
    
    }
        

}
