<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class CurrencyFormRequest extends FormRequest
{
    
    public function rules(Request $request = null): array
    {
        return [
            'currency_name' => 'required|string|max:15|unique:currencies',
            'currency_symbol' => 'required|string|max:5|unique:currencies',
        ];
    }
  

}
