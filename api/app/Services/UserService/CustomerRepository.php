<?php

namespace App\Services\UserService;
use App\Models\Customer;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;





class CustomerRepository
{
    public function index($type){

    
        return Customer::ofType($type)->latest()->paginate(1);
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
    public function searchCustomer($searchCriteria)
    {
       
       
    $user = Customer::where(function($query) use ($searchCriteria) {
                    $query->where('first_name', 'like', '%' . $searchCriteria . '%')
                        ->orWhere('last_name', 'like', '%' . $searchCriteria . '%')
                        ->orWhere('contact_person', 'like', '%' . $searchCriteria . '%')
                        ->orWhere('company_name', 'like', '%' . $searchCriteria . '%');
                })
                ->get();
        
            
            return $user;

      
    }
    

   
}
