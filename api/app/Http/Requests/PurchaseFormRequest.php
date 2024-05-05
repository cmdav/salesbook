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
    
            // 'product_type_id' => 'required|uuid',
            // 'supplier_id' => 'nullable|uuid',
            // 'price' => 'required|integer',
            // 'batch_no' => 'required|string|max:50',
            // 'quantity' => 'required|integer',
            // 'product_identifier' => 'nullable|string|max:50',
            // 'expired_date' => 'nullable|date|after_or_equal:today',

            //'purchase_by' => 'required|uuid',
            
                'purchases' => 'required|array|min:1',
                'purchases.*.product_type_id' => 'required|string',
                'purchases.*.supplier_id' => 'nullable|uuid',
                'purchases.*.price_id' => 'required',
                'purchases.*.cost_price' => 'required',
                'purchases.*.batch_no' => 'required|string|max:50',
                'purchases.*.quantity' => 'required|integer',
                'purchases.*.product_identifier' => 'nullable|string|max:50',
                'purchases.*.expiry_date' => [
                    'nullable',
                    'date',
                    'after_or_equal:today'
                ],
            
           
           
        ];

    }
    public function messages(){

        return [

          
        ];
    }
  

}
