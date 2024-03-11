<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class PurchaseFormRequest extends FormRequest
{
    
    public function rules(Request $request): array
    {
        return [
    
            'product_type_id' => 'required|uuid',
            'supplier_id' => 'nullable|uuid',
            'price_id' => 'required|uuid',
            'currency_id' => 'required|uuid',
            'discount' => 'required|integer',
            'batch_no' => 'required|string|max:50',
            'quantity' => 'required|integer',
            'product_identifier' => 'nullable|string|max:50',
            'expired_date' => 'nullable|date',
            'purchase_owner' => 'required|uuid',
           
           
        ];

    }
    public function messages(){

        return [

          
        ];
    }
  

}
