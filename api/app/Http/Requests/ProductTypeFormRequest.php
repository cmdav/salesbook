<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class ProductTypeFormRequest extends FormRequest
{
    public function rules(Request $request): array
    {

        $productTypeRule = [
            'required',
            'string',
            'max:50',
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

        return [

            'product_type_name' => $productTypeRule,
            'product_type_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
              'product_type_description' => 'required|string|max:65535',

            'barcode' => $barcodeRule,
            'organization_id' => 'nullable|uuid|exists:organizations,id',
'supplier_id' => 'nullable|uuid|exists:suppliers,id',
'selling_unit_capacity_id' => 'required|integer|exists:selling_unit_capacities,id',
'purchase_unit_id' => 'required|uuid|exists:purchase_units,id',
'selling_unit_id' => 'required|uuid|exists:selling_units,id',
'category_id' => 'required|uuid|exists:product_categories,id',
'sub_category_id' => 'required|uuid|exists:product_sub_categories,id',

        ];
    }
}
