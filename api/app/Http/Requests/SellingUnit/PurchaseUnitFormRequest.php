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
                Rule::unique('purchase_units')
                    ->where('parent_purchase_unit_id', $this->input('parent_purchase_unit_id'))
                    ->ignore($this->route('purchase_unit')), // Ignore current entry during update
            ],
            'measurement_group_id' => [
                'required',
                'uuid',
                function ($attribute, $value, $fail) {
                    // Check if parent_purchase_unit_id is NULL (this is a top-level unit)
                    if (is_null($this->input('parent_purchase_unit_id'))) {
                        // If it's a top-level unit, check if there's already an existing parent with NULL parent_purchase_unit_id
                        $existingParent = \DB::table('purchase_units')
                            ->whereNull('parent_purchase_unit_id') // parent_purchase_unit_id is NULL
                            ->where('measurement_group_id', $this->input('measurement_group_id'))
                            ->exists();

                        if ($existingParent) {
                            $fail('Only one top-level parent unit is allowed for each measurement group.');
                        }
                    }
                }
            ],

            // Custom validation for parent_purchase_unit_id (ensure only one NULL parent per group)
            'parent_purchase_unit_id' => [
                'nullable', // The field can be missing or null
                'uuid',
                'exists:purchase_units,id', // Ensure the provided parent ID exists in the purchase_units table
                function ($attribute, $value, $fail) {
                    // Check if the parent_purchase_unit_id is null, which signifies the first-level parent
                    if (!is_null($value)) {
                        $parentUnits = \DB::table('purchase_units')
                            ->where('parent_purchase_unit_id', $value)
                            ->where('measurement_group_id', $this->input('measurement_group_id'))
                            ->count();

                        if ($parentUnits >= 1) {
                            $fail('Only one unit is allowed per level in the hierarchy.');
                        }
                    }
                }
            ],

            'unit' => 'required|integer|min:0', // Ensure the unit is an integer and defaults to 0 if not provided
        ];
    }
}
