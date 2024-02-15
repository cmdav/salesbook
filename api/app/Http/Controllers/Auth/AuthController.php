<?php

namespace App\Http\Controllers\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\AuthFormRequest;
use App\Services\AuthService\AuthService;


class AuthController extends Controller
{
    
    protected AuthService $authService;

    public function __invoke(AuthService $authService, AuthFormRequest $request)
    {
        
        $this->authService = $authService;

        $response = $this->authService->authenticateUser($request->all());
        
        return $response;
      

    }
   
  
   
}
