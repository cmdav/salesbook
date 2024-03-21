<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class SaleFormRequest extends FormRequest
{
    
    public function rules(Request $request): array
    {
        return [
    
            'product_type_id' => 'required|uuid',
            'customer_id' => 'nullable|uuid',
            'price_sold_at' => 'required|integer',
            'quantity' => 'required|integer',
            'payment_method' => 'required|string',
            //'sales_owner' => 'required|uuid',
           
        ];

    }
    public function messages(){

        return [

            'account_number'=>'Account number must be 10 digit'
        ];
    }
  

}
