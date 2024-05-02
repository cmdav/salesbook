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
        'organization_type' => [
            Rule::requiredIf(!$request->filled('role_id')), // Require this field if 'role_id' is not filled
            'string',
            'in:sole_properietor,company,sales_personnel,supplier'
        ],
       
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
   
    if ($this->input('organization_type') === 'supplier') {
      
        $rules['email'] = [
            'required',
            'email',
            'max:255',
            function ($attribute, $value, $fail) {
              
                $exists = \App\Models\User::where('email', $value)
                                          ->where('organization_id', $this->input('organization_id'))
                                          ->exists();
                if (!$exists) {
                    $fail($attribute . ' is invalid or does not match the given organization.');
                }
            }
        ];
    }
    
    else {
       
        $rules['email'] = [
            'required',
            'email',
            'max:255',
            Rule::unique('users')->ignore($this->user() ? $this->user()->id : null)
        ];
    }





     if ($request->input('organization_type') == 'company') { 

        $rules['contact_person'] = 'required|string|max:55';
        $rules['company_name'] = 'required|string|max:55';
        $rules['company_address'] = 'required|string|max:55';
    }

    if ($request->input('organization_type') == 'sole_properietor'  || $request->input('organization_type') == 'sales_personnel') { 

        $rules['first_name'] = 'required|string|max:55';
        $rules['last_name'] = 'required|string|max:55';
        $rules['middle_name'] = 'nullable|string|max:55';
        $rules['dob'] = 'nullable|date|max:55';
      
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
