<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MeasurementFormRequest extends FormRequest
{
    public function rules(): array
    {
       
        $measurementId = $this->route('measurement');

        return [
            'measurement_name' => [
                'required',
                'string',
                'max:30',
                'regex:/^[^\s]/',
                Rule::unique('measurements')->ignore($measurementId)
            ],
            'unit' => [
                'required',
                'string',
                'max:5',
                'regex:/^[^\s]/',
                Rule::unique('measurements')->ignore($measurementId)
            ],
        ];
    }
}
