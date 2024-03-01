<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class UserFormRequest extends FormRequest
{
    
    public function rules(Request $request): array
{
    // Default rules
    $rules = [
       
        'dob' => 'nullable|date|date_format:Y-m-d',
        'phone_number'=>'nullable|string',
        'type_id' => 'required|integer',
        'email' => ['required', 'email', 'max:55', Rule::unique('users')->ignore($this->user)],
    ];
    // register company customer
    if ($request->input('role_id') == 1) { 

        $rules['contact_person'] = 'required|string|max:55';
        $rules['company_name'] = 'required|string|max:55';
      
    }else{

        $rules['first_name'] = 'required|string|max:55';
        $rules['last_name'] = 'required|string|max:55';
        $rules['middle_name'] = 'nullable|string|max:55';

    }
     //2 company 1 for supplier 0 for customer
     if ($request->input('type_id') == 2) { 

        $rules['organization_code'] = 'required|integer';
		 $rules['phone_number'] = 'required|string|unique:users';
      
    }
   
    //supplier and company    
    if ($request->input('type_id') > 0) { 
            
        $rules['password'] = [
            'required',
            'string',
            'min:8',
            'max:30',
            'confirmed',
            'regex:/[a-z]/',      // must contain at least one lowercase letter
            'regex:/[A-Z]/',      // must contain at least one uppercase letter
            'regex:/[0-9]/',      // must contain at least one digit
            'regex:/[@$!%*#?&]/', // must contain a special character
        ];
      
    }
        //supplier
    if ($request->input('type_id') == 1) { 
            
        $rules['email'] = [
            'required',
            'email',
            Rule::exists('users')->where(function ($query) use ($request) {
                $query->where('organization_id', $request->input('organization_id'));
            }),
        ];
         $rules['organization_id'] = 'required|uuid';
        }
     //customer
    if ($request->input('type_id') == 0) { 

        $rules['organization_code'] = 'nullable';
        $rules['organization_id'] = 'required|uuid';
    }
    

    return $rules;
}

    public function messages()
    {
        return [
            'password.regex' => 'The :attribute must include at least one uppercase letter, one lowercase letter, one number, and one special character. Your password should be 8 to 30 characters long.',
        ];
    }

}
