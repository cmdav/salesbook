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
            // 'selling_price' => ['nullable', 'integer', function ($attribute, $value, $fail) use ($request) {
            //     // Check if both selling_price and system_price are greater than zero
            //     if ($value > 0 && $request->input('system_price') > 0) {
            //         $fail('Either selling price or system price can be set greater than zero, not both.');
            //     }
            //     // Check if both selling_price and system_price are zero or not set
            //     if ($value <= 0 && (!$request->has('system_price') || $request->input('system_price') <= 0)) {
            //         $fail('One of selling price or system price must be greater than zero.');
            //     }
            // }],
           
           // 'auto_generated_selling_price' => ['nullable', 'integer', 'between:0,100'],
            'auto_generated_selling_price' => ['nullable', 'integer', 'between:0,100'],
            'currency_id' => 'required|string',
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
