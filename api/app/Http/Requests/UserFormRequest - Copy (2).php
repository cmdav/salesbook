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
        return [
            'first_name' => 'required|string|max:55',
            'last_name' => 'required|string|max:55',
            'middle_name' => 'nullable|string|max:55',
            'organization_code' => 'required|integer',
            'dob' => 'nullable|date|date_format:Y-m-d',
            'email' => 'required|email|max:55|unique:users',
            'password' => [
                'required',
                'string',
                'min:8', // minimum 8 characters
                'max:30', // maximum 30 characters
                'confirmed',
                'regex:/[a-z]/',      // must contain at least one lowercase letter
                'regex:/[A-Z]/',      // must contain at least one uppercase letter
                'regex:/[0-9]/',      // must contain at least one digit
                'regex:/[@$!%*#?&]/', // must contain a special character
            ],
        
        ];
    }
    public function messages()
    {
        return [
            'password.regex' => 'The :attribute must include at least one uppercase letter, one lowercase letter, one number, and one special character. Your password should be 8 to 30 characters long.',
        ];
    }

}
