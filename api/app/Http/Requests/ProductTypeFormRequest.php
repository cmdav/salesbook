<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class ProductTypeFormRequest extends FormRequest
{
    
    public function rules(Request $request): array
    {
        return [
    
            'product_id' => 'required|uuid',
            'product_type' => 'required|string|max:50',
        'product_type_image' => 'nullable|string|max:150',
            'product_type_description' => 'required|string',
            'organization_id' => 'nullable|uuid',
            'supplier_id' => 'nullable|uuid',
           
        ];

    }
    public function messages(){

        return [

            'account_number'=>'Account number must be 10 digit'
        ];
    }
  

}
