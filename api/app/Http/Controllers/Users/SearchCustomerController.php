<?php

namespace App\Http\Controllers\Users;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\UserService\CustomerService;


class SearchCustomerController extends Controller
{
    
    protected CustomerService $CustomerService;

    public function __invoke(CustomerService $CustomerService, $criteria, Request $request)
    {
        
        $this->CustomerService = $CustomerService;
        if($customerNames =$this->CustomerService->searchCustomer($criteria, $request->all())){
            
            return response()->json($customerNames);
        }
        return response()->json(['message'=>'Customer not found'], 404);
        
         

    }

   
  
   
}
