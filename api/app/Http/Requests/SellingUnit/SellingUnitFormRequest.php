<?php

namespace App\Http\Requests\SellingUnit;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SellingUnitFormRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {

        return [
            'purchase_unit_id' => [
                'required',
                'uuid',
                'exists:purchase_units,id',
            ],
            'selling_unit_name' => [
                'required',
                'string',
                'max:50',
                Rule::unique('selling_units')
                    ->where('purchase_unit_id', $this->purchase_unit_id)
                    ->ignore($this->route('selling_unit')),
            ],
        ];

    }
}
