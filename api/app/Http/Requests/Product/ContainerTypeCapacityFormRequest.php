<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class ContainerTypeCapacityFormRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'container_type_id' => 'required|uuid|exists:container_types,id',
            'container_capacity' => 'required|integer|min:1',
        ];
    }
}