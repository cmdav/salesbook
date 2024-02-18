<?php

namespace App\Http\Controllers\Email;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\UserService\UserService;
use Illuminate\Support\Facades\Mail;
use App\Mail\NewUserHasRegisterEmail;
use App\Services\EncryptDecryptService;
use App\Services\ExpiryService;
use Carbon\Carbon;


class EmailVerificationController extends Controller
{
    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;

    }
   
    // To resend the activation link
    public function store(Request $request)
    {
        $request->validate([
            'email'=> 'required|email|exists:users'
        ]);

        $user = $this->userService-> authenticateUser($request->email);
        
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        if ($user->email_verified_at) {
            return response()->json(['message' => 'Email already verified.'], 422);
        }

        $user->touch();

        Mail::to($user->email)->send(new NewUserHasRegisterEmail($user));
        
        return response()->json(['message' => 'Verification link resent successfully.']);
    }



    // To update the email_verified_at field using the user's email
    public function update($hash)
    {
       
        $decryptedEmail = EncryptDecryptService::decryptvalue($hash);

        
        $decryptedValue = explode("..", $decryptedEmail); // 0 for email 1 time it was updated
       

        $user = $this->userService->authenticateUser( $decryptedValue[0]);
        
        if (!$user) {

            return response()->json(['message' => 'Invalid hash '], 404);
        }

        if(ExpiryService::hasLinkExpiry($user->updated_at)){

            return response()->json(['message' => 'Verification link has expired'], 401);
        }
        $user->email_verified_at = Carbon::now();
        $user->save();

        return response()->json(['message' => 'Email verified successfully.']);
    }
}
