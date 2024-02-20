<?php

namespace App\Http\Controllers\Email;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\UserService\UserService;
use App\Services\EmailService;
use Carbon\Carbon;


class ResendEmailController extends Controller
{
    protected UserService $userService;
    protected EmailService $emailService;

    public function __construct(UserService $userService, EmailService $emailService)
    {
        $this->userService = $userService;
        $this->emailService = $emailService; 
    }
    
   
    // To resend the activation link
    public function __invoke(Request $request)
    {
       

        $user = $this->userService-> authenticateUser($request->email);
        
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        if ($user->email_verified_at) {
            return response()->json(['message' => 'Email already verified.'], 422);
        }
        // resend registration email
        if ($this->emailService->sendEmail($user, 2)) {

            $user->touch();
            return response()->json(['message' => 'Verification link resent successfully.']);

        } else {
           
            return response()->json(['message' => 'Failed to send verification link.'], 500);
        }
    }

}
