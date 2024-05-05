<?php

namespace App\Services\UserService;
use App\Models\Customer;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;





class CustomerRepository
{
    public function index($type){

        return Customer::ofType($type)->latest()->paginate(20);
    }
    public function create($data){

        return Customer::create($data);
    }
    public function customerName()
    {
    
        return Customer::select('id', 
        \DB::raw("CONCAT_WS(' ', first_name, last_name, contact_person) AS customer_detail"))
        ->latest()
        ->get();
       
    }
    

   
}
