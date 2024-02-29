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
    
            'product_id' => 'required|uuid',
            'store_id' => 'nullable|uuid',
            'quantity_available' => 'required|integer',
        ];

    }
   
  

}
