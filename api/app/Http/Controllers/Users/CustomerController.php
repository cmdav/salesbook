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
    public function index(Request $request){

        //return $request->all();
        // $validatedData = $request->validate([
        //     'type' => 'required|in:individual,company'
        // ]);
        
         return $this->CustomerService->index($request->type);
     
    }
    public function store(Request $request){

        $rules = [
            'first_name' => 'nullable|string|max:55',
            'company_name' => 'nullable|string|max:55',
            'contact_person' => 'nullable|string|max:55',
            'address' => 'nullable|string|max:55',
            'last_name' => 'nullable|string|max:55',
            'middle_name' => 'nullable|string|max:55',
            'phone_number' => 'nullable|string|max:15',
            'type_id' => 'required|string|in:individual,company',
            'email' => 'nullable|email|unique:customers',
        ];
    
        // Validate the request data
        $validatedData = $request->validate($rules);
        
        $data = $this->CustomerService->create($request->all());
        return response()->json(['data'=>$data, 'message'=>'Submission successful']);
    
   }
}

