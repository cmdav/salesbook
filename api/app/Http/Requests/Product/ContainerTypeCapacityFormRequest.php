<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ContainerTypeCapacityFormRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'container_type_id' => [
                'required',
                'uuid',
                Rule::exists('container_types', 'id'),
            ],
            'container_capacity' => [
                'required',
                'integer',
                'min:1',
                // Custom rule to ensure unique combination of container_type_id and container_capacity
                Rule::unique('container_type_capacities')->where(function ($query) {
                    return $query->where('container_type_id', $this->container_type_id)
                                 ->where('container_capacity', $this->container_capacity);
                }),
            ],
        ];
    }
}
