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
            'measurement_group_id' => 'nullable|uuid', // Corrected the typo from 'nullabe' to 'nullable' and added UUID validation

            // Adding the new rules for parent_purchase_unit_id and unit
            'parent_purchase_unit_id' => 'nullable|uuid|exists:purchase_units,id', // Ensures it's a valid UUID and exists in the 'purchase_units' table
            'unit' => 'nullable|integer|min:0', // Ensures the unit is an integer and defaults to 0 if not provided
        ];
    }


}
