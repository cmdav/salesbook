<?php

namespace App\Services\UserService;
use App\Models\Customer;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;





class CustomerRepository
{
    public function index($request){

        $branchId = 'all';
        if(isset($request['branch_id']) &&  auth()->user()->role->role_name == 'Admin'){
            $branchId = $request['branch_id']; 
        }
        else if(auth()->user()->role->role_name != 'Admin'){
            $branchId = auth()->user()->branch_id; 
        }

        if (isset($request['type'])) {
          
            $query = Customer::ofType($request['type'])
            ->with('branches:id,name');
           
            if ($branchId !== 'all') {
                // Apply the where clause if branch_id is not 'all' and the user is not admin
                $query->where('branch_id', $branchId);
            }
            return $query->latest()->paginate(20);

            // Transform the collection to include the branch name directly in the customer data
            $customers->getCollection()->transform(function ($customer) {
                $customer->branch_name = $customer->branches->name;
                unset($customer->branches);
                return $customer;
            });
        
            return $customers;
        } else {
            // Handle the case where 'type' is not present in the request
            return response()->json(['success' => false, 'message'=>'Type is required'], 400);
        }
    }
    public function create($data){

        return Customer::create($data);
    }
    public function customerName($request)
    {
        $branchId = isset($request['branch_id']) ? $request['branch_id'] : auth()->user()->branch_id;
        return Customer::select('id', 
        \DB::raw("CONCAT_WS(' ', first_name, last_name, contact_person) AS customer_detail"))
        ->where('branch_id', $branchId)
        ->latest()
        ->get();
       
    }
    public function searchCustomer($searchCriteria, $request)
    {
       
        $branchId = isset($request['branch_id']) ? $request['branch_id'] : auth()->user()->branch_id;
    $user = Customer::where(function($query) use ($searchCriteria) {
                    $query->where('first_name', 'like', '%' . $searchCriteria . '%')
                        ->orWhere('last_name', 'like', '%' . $searchCriteria . '%')
                        ->orWhere('contact_person', 'like', '%' . $searchCriteria . '%')
                        ->orWhere('company_name', 'like', '%' . $searchCriteria . '%');
                })->where('branch_id', $branchId)
                ->get();
        
            
            return $user;

      
    }
    

   
}
