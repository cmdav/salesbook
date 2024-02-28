<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class StoreFormRequest extends FormRequest
{
    
    public function rules(Request $request): array
    {
        return [
          
            'supplier_product_id' => 'required|uuid|exists:supplier_products,id',
            'currency' => 'required|uuid|exists:currencies,id',
            'discount' => 'required|integer|min:0|max:100',
            'batch_no' => 'required|string|max:50',
            'product_identifier' => 'required|string|max:50',
            'supplier_price' => 'required|integer|min:0', 
            'expired_date' => 'nullable|date',
        ];
    }
   

}
