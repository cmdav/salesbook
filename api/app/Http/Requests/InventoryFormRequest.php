<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class InventoryFormRequest extends FormRequest
{
    
    public function rules(Request $request): array
    {
        return [
    
            'bank_name' => ['required', 'string', 'max:55'],
            'account_number' => ['required', 'integer', 'digits_between:10,10'],
            'account_name' => ['required', 'string', 'max:255'],
            'state' => ['nullable', 'string', 'max:55'],
            'address' => ['nullable', 'string', 'max:255'],
            'dob' => ['nullable', 'date'],
        ];

    }
    public function messages(){

        return [

            'account_number'=>'Account number must be 10 digit'
        ];
    }
  

}
