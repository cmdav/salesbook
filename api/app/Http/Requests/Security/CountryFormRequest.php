<?php

namespace App\Http\Requests\Security;

use Illuminate\Foundation\Http\FormRequest;

class CountryFormRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            // Validation rules
        ];
    }
}