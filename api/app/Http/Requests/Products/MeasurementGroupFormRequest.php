<?php

namespace App\Http\Requests\Products;

use Illuminate\Foundation\Http\FormRequest;

class MeasurementGroupFormRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules(): array
    {
        $groupId = $this->route('measurement_group'); // Get the group ID for update purposes

        return [
            'group_name' => [
                'required',
                'string',
                'max:100',
                "unique:measurement_groups,group_name,{$groupId},id", // Unique constraint with an exception for the current group on update
            ],
            'created_by' => 'nullable|uuid',
            'updated_by' => 'nullable|uuid',
        ];
    }

}
