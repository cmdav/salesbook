<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

class UserFormRequest extends FormRequest
{
    
    public function rules(Request $request): array
{
    // Default rules
    $rules = [
       
        'dob' => 'nullable|date|date_format:Y-m-d',
        'phone_number'=>'nullable|string',
        'organization_type' => 'required|string|in:sole_properietor,company,sales_personnel',
        'email' => ['required', 'email', 'max:55', Rule::unique('users')->ignore($this->user)],
        'password' => 
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
   
     
     if ($request->input('organization_type') == 'company') { 

        $rules['contact_person'] = 'required|string|max:55';
        $rules['company_name'] = 'required|string|max:55';
        $rules['company_address'] = 'required|string|max:55';
    }
    //1 for business 0 for sole_properietor
    if ($request->input('organization_type') == 'sole_properietor'  || $request->input('organization_type') == 'sales_assistant') { 

        $rules['first_name'] = 'required|string|max:55';
        $rules['last_name'] = 'required|string|max:55';
        $rules['middle_name'] = 'nullable|string|max:55';
      
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
