<?php

namespace App\Http\Controllers\Users;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\UserFormRequest;
use App\Services\UserService\UserService;

class UserController extends Controller
{
    
    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;

    }
   
    public function store(UserFormRequest $request)
    { 	
        
            return $this->userService->createUser($request->all());
        
    }
   
}
