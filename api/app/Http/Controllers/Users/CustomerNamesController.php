<?php

namespace App\Http\Controllers\Users;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\UserService\CustomerService;


class CustomerNamesController extends Controller
{
    
    protected CustomerService $CustomerService;

    public function __invoke(CustomerService $CustomerService)
    {
        
        $this->CustomerService = $CustomerService;
        if($customerNames =$this->CustomerService->customerName()){
            return response()->json($customerNames);
        }
        return response()->json(['message'=>'JobRole not found'], 404);
        
         

    }

   
  
   
}
