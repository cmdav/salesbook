<?php

namespace App\Http\Requests\Security;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PaymentMethodFormRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = [
            'payment_name' => [
                'required',
                'string',
                Rule::unique('payment_methods', 'payment_name')->ignore($this->route('payment_method')),
            ],
        ];

        return $rules;
    }
}
