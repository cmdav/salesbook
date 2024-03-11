<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class PriceFormRequest extends FormRequest
{
    
    public function rules(Request $request): array
    {
        return [
    
            'product_type_id' => 'required|uuid',
            'supplier_id' => 'nullable|uuid',
            'product_type_price' => 'nullable|integer',
            'system_price' => 'nullable|integer',
            'currency_id' => 'required|uuid',
            'discount' => 'required|integer',
            'organization_id' => 'nullable|uuid',
           
        ];

    }
    public function messages(){

        return [

            'account_number'=>'Account number must be 10 digit'
        ];
    }
  

}
