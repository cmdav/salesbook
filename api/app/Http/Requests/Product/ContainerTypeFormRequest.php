<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ContainerTypeFormRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'container_type_name' => [
                'required',
                'string',
                'max:50',
                Rule::unique('container_types')->ignore($this->route('container_type'))
            ],
        ];
    }
}