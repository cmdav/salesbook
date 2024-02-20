<?php

namespace App\Http\Controllers\Email;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\UserService\UserService;
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

    
    public function __invoke($hash)
    {
        

        $token = EncryptDecryptService::decryptvalue($hash);

       

        $user = $this->userService->getUserByToken( $token);
        
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
