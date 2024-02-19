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
            'password' => 'required|string|confirmed|min:8|max:30'
        
        ];
    }


}
