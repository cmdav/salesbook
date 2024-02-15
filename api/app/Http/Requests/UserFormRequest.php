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
            'email'=>'required|email|max:55|unique:users',
            'name' => 'required|string|max:55',
            'password' => 'required|string|min:2|max:60'
        
        ];
    }


}
