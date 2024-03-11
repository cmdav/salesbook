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
          
            'purchase_id' => 'required|uuid',
            'currency' => 'required|uuid',
            'store_owner' => 'required|uuid',
            'quantity_available' => 'nullable|integer|min:0',
            'store_type' => 'required|integer|in:0,1',
            'organization_id' => 'nullable|uuid',
           
        ];
    }
   

}
