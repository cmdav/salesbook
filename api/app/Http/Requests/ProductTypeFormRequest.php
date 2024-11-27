<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

class ProductTypeFormRequest extends FormRequest
{
    public function rules(Request $request): array
    {


        $productTypeRule = [
            'required',
            'string',
            'max:250',
            Rule::unique('product_types')->ignore($this->route('product_type'))
        ];

        if ($this->getMethod() === 'PUT') {
            // When updating, exclude the current product's ID and product type
            $productTypeRule[] = Rule::ignore($this->route('product_type'));
        }

        $barcodeRule = [
            'nullable',
            'string',
            Rule::unique('product_types')->ignore($this->route('product_type'))
        ];

        if ($this->getMethod() === 'PUT') {
            // When updating, exclude the current product's barcode from the uniqueness check
            $barcodeRule[] = Rule::ignore($this->route('product_type'), 'barcode');
        }

        $uniqueUnitCombinationRule = function ($attribute, $value, $fail) {
            $purchaseUnitIds = $this->input('purchase_unit_id');
            $sellingUnitIds = $this->input('selling_unit_id');

            if (count($purchaseUnitIds) !== count($sellingUnitIds)) {
                $fail('The number of purchase units and selling units must match.');
                return;
            }

            // Combine the purchase_unit_id and selling_unit_id into a single array of pairs
            $unitCombinations = [];
            foreach ($purchaseUnitIds as $index => $purchaseUnitId) {
                $unitCombinations[] = [
                    'purchase_unit_id' => $purchaseUnitId,
                    'selling_unit_id' => $sellingUnitIds[$index],
                ];
            }

            // Check if there are any duplicate combinations within the array
            $duplicates = collect($unitCombinations)
                ->duplicates()
                ->values()
                ->all();

            if (!empty($duplicates)) {
                $fail('The combination of purchase unit and selling unit must be unique in the form submission.');
            }
        };


        return [

            'product_type_name' => $productTypeRule,
            'product_type_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'product_type_description' => 'required|string|max:65535',

            'barcode' => $barcodeRule,
            'organization_id' => 'nullable|uuid|exists:organizations,id',
            'supplier_id' => 'nullable|uuid|exists:suppliers,id',
            'category_id' => 'nullable|uuid|exists:product_categories,id',
            'sub_category_id' => 'nullable|uuid|exists:product_sub_categories,id',

            'selling_unit_capacity_id' => 'required|array',
            'selling_unit_capacity_id.*' => 'integer|exists:selling_unit_capacities,id',


            'purchase_unit_id.*' => 'uuid|exists:purchase_units,id',


            'selling_unit_id.*' => 'uuid|exists:selling_units,id',
            'purchase_unit_id' => ['required', 'array', $uniqueUnitCombinationRule],
            'selling_unit_id' => ['required', 'array', $uniqueUnitCombinationRule],



        ];
    }
}
