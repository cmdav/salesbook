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
        'first_name' => 'required|string|max:55',
        'last_name' => 'required|string|max:55',
        'middle_name' => 'nullable|string|max:55',
        'dob' => 'nullable|date|date_format:Y-m-d',
        'type_id' => 'required|integer',
        'email' => ['required', 'email', 'max:55', Rule::unique('users')->ignore($this->user)],
    ];

    // customer
    if ($request->input('type_id') == 0) { 

        $rules['organization_code'] = 'required|integer';
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
            
            
        }
    //company
    if ($request->input('type_id') == 2) { 
        
        $rules['organization_code'] = 'required|integer';
      
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
