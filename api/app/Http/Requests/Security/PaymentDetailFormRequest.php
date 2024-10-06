<?php

namespace App\Http\Requests\Security;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PaymentDetailFormRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'payment_method_id' => 'required|uuid|exists:payment_methods,id',
            'account_name' => 'nullable|string',
            'account_number' => 'nullable|string',
            'payment_identifier' => [
                'required',
                'string',
                Rule::unique('payment_details', 'payment_identifier')->ignore($this->route('payment_detail')),
            ],
        ];
    }
}
