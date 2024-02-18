<?php

namespace App\Http\Controllers\Email;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;


class ForgotPasswordController extends Controller
{
    
   

    public function __invoke(Request $request)
    {
        
        
          

            return response()->json(['message' => 'Logout successful']);
      

    }
   
  
   
}
