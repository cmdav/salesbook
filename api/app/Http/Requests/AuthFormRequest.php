<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



class AuthFormRequest extends FormRequest
{
    
    public function rules(Request $request): array
    {
        return [
            'email' => [
                'required',
                'email',
                'max:55',
                Rule::exists('users', 'email')
            ],
            'password' => 'required|string|min:2|max:60'
        
        ];
    }
    public function messages(): array
    {
        return [
            'email.exists' => 'Invalid credentials',
        ];
    }


}
