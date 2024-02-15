<?php

namespace App\Http\Controllers\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;


class LogOutController extends Controller
{
    
   

    public function __invoke(Request $request)
    {
        
        
            $accessToken = $request->bearerToken();

            $token = PersonalAccessToken::findToken($accessToken);
           
            $token->delete();

            return response()->json(['message' => 'Logout successful']);
      

    }
   
  
   
}
