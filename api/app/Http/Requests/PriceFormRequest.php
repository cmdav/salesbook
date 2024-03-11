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
    
            'store_id' => 'required|uuid',
            'organization_id' => 'required|uuid',
            'customer_id' => 'required|uuid',
            'price' => 'required|integer',
            'quantity' => 'required|integer',
            'sales_owner' => 'required|uuid',
           
        ];

    }
    public function messages(){

        return [

            'account_number'=>'Account number must be 10 digit'
        ];
    }
  

}