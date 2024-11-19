<?php

namespace App\Http\Requests\SellingUnit;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PurchaseUnitFormRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'purchase_unit_name' => [
                'required',
                'string',
                'max:50',
                Rule::unique('purchase_units')->ignore($this->route('purchase_unit')),
            ],
           'measurement_group_id' => 'required'
        ];
    }
}
