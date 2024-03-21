<?php

namespace App\Http\Controllers\Users;
use App\Http\Controllers\Controller;
use App\Http\Requests\CustomerFormRequest;
use App\Services\UserService\CustomerService;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    protected CustomerService $CustomerService;

    

    public function __construct(CustomerService $CustomerService)
    {
        $this->CustomerService = $CustomerService;

     
    }
    public function index(){
         return $this->CustomerService->index();
     
    }
    public function store(Request $request){
        
        return $this->CustomerService->create($request->all());
    
   }
}

