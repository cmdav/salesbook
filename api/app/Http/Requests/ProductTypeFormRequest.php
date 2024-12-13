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
            $productTypeRule[] = Rule::ignore($this->route('product_type'));
        }

        $barcodeRule = [
            'nullable',
            'string',
            Rule::unique('product_types')->ignore($this->route('product_type'))
        ];

        if ($this->getMethod() === 'PUT') {
            $barcodeRule[] = Rule::ignore($this->route('product_type'), 'barcode');
        }

        return [
            'product_type_name' => $productTypeRule,
            'product_type_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'vat' => 'required',
            'product_type_description' => 'required|string|max:65535',
            'barcode' => $barcodeRule,
            'organization_id' => 'nullable|uuid|exists:organizations,id',
            'supplier_id' => 'nullable|uuid|exists:suppliers,id',
            'category_id' => 'nullable|uuid|exists:product_categories,id',
            'sub_category_id' => 'nullable|uuid|exists:product_sub_categories,id',
            'purchase_unit_id' => ['required', 'array'],
            'purchase_unit_id.*' => [
                'uuid',
                'exists:purchase_units,id',
                function ($attribute, $value, $fail) use ($request) {
                    $purchaseUnitIds = $request->input('purchase_unit_id');
                    if (!empty($purchaseUnitIds)) {
                        $measurementGroups = DB::table('purchase_units')
                            ->whereIn('id', $purchaseUnitIds)
                            ->pluck('measurement_group_id')
                            ->unique();

                        if ($measurementGroups->count() > 1) {
                            $fail('All purchase units must belong to the same measurement group.');
                        }
                    }
                },
            ],
        ];
    }

}
