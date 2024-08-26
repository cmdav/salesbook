<?php

namespace App\Http\Requests\SellingUnit;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SellingUnitCapacityFormRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'selling_unit_id' => [
                'required',
                'uuid',
                'exists:selling_units,id',
            ],
            'selling_unit_capacity' => [
                'required',
                'integer',
                Rule::unique('selling_unit_capacities')
                    ->where('selling_unit_id', $this->selling_unit_id)
                    ->ignore($this->route('selling_unit_capacity')),
            ],
        ];
    }
}
