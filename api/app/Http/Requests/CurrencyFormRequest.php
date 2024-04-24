<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CurrencyFormRequest extends FormRequest
{
    public function rules(): array
    {
       
        $currencyId = $this->route('currency'); 

        return [
            'currency_name' => [
                'required',
                'string',
                'max:15',
                'regex:/^[^\s]/',
                Rule::unique('currencies')->ignore($currencyId) 
            ],
            'currency_symbol' => [
                'required',
                'string',
                'max:5',
                'regex:/^[^\s]/',
                Rule::unique('currencies')->ignore($currencyId) 
            ],
        ];
    }
}
