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
        $productIdRule = 'required|string';
        $productTypeRule = [
            'required',
            'string',
            'max:50',
            Rule::unique('product_types')->where(function ($query) use ($request) {
                return $query->where('product_id', $request->product_id);
            })
        ];

        if ($this->getMethod() === 'PUT') {
            // When updating, exclude the current product's ID and product type
            $productTypeRule[] = Rule::ignore($this->route('product_type'));
        }

        $barcodeRule = [
            'nullable',
            'string',
            Rule::unique('product_types')->where(function ($query) use ($request) {
                return $query->where('product_id', $request->product_id);
            })
        ];

        if ($this->getMethod() === 'PUT') {
            // When updating, exclude the current product's barcode from the uniqueness check
            $barcodeRule[] = Rule::ignore($this->route('product_type'), 'barcode');
        }

        return [
            'product_id' => $productIdRule,
            'product_type_name' => $productTypeRule,
            'product_type_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'product_type_description' => 'required|string',
            'barcode' => $barcodeRule,
            'organization_id' => 'nullable|string',
            'supplier_id' => 'nullable|string',
            'selling_unit_capacity_id' => 'required|integer',
            'purchase_unit_id' => 'required',
        ];
    }
}
