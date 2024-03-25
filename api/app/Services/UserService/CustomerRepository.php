<?php

namespace App\Services\UserService;
use App\Models\Customer;
use Illuminate\Database\QueryException;





class CustomerRepository
{
    public function index(){

        return Customer::paginate(2);
    }
    public function create($data){

        return Customer::create($data);
    }
    

   
}