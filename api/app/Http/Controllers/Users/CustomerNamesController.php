<?php

namespace App\Http\Controllers\Users;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\UserService\CustomerService;


class CustomerNamesController extends Controller
{
    
    protected CustomerService $CustomerService;

    public function __invoke(CustomerService $CustomerService, Request $request)
    {

        $this->CustomerService = $CustomerService;
        if($customerNames =$this->CustomerService->customerName($request->all())){
            return response()->json($customerNames);
        }
        return response()->json(['message'=>'JobRole not found'], 404);
        
         

    }

   
  
   
}
