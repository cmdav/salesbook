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
    
            'product_type_id' => 'required|string',
            'supplier_id' => 'nullable|string',
            'cost_price' => 'required|integer',
            'selling_price' => 'required|integer',
           // 'auto_generated_selling_price' => ['nullable', 'integer', 'between:0,100'],
            'auto_generated_selling_price' => ['nullable', 'integer'],
            //'currency_id' => 'required|string',
            'discount' => 'nullable|integer',
            'organization_id' => 'nullable|string',
           
        ];

    }
    public function messages(){

        return [

            //'account_number'=>'Account number must be 10 digit'
        ];
    }
  

}
