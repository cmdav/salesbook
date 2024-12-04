<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;

class PurchaseFormRequest extends FormRequest
{
    public function rules(Request $request): array
    {
        return [
            'purchases' => 'required|array|min:1',
            'purchases.*.product_type_id' => 'required|string',
            'purchases.*.supplier_id' => 'nullable|uuid',
            'purchases.*.batch_no' => 'required|string|max:50',
           // 'purchases.*.purchase_unit_id' => 'required|uuid|max:50',
            'purchases.*.product_identifier' => 'nullable|string|max:50',
            'purchases.*.expiry_date' => [
                'nullable',
                'date',
                'after_or_equal:today'
            ],
            'purchases.*.purchase_unit_data' => 'required|array',
            'purchases.*.purchase_unit_data.*' => 'array',
            'purchases.*.purchase_unit_data.*.capacity_qty' => 'required|numeric',
            'purchases.*.purchase_unit_data.*.cost_price' => 'nullable|numeric|min:0|required_without:purchases.*.purchase_unit_data.*.price_id',
            'purchases.*.purchase_unit_data.*.selling_price' => 'nullable|numeric|min:0|required_without:purchases.*.purchase_unit_data.*.price_id',
            'purchases.*.purchase_unit_data.*.price_id' => 'nullable|uuid|required_without_all:purchases.*.purchase_unit_data.*.cost_price,purchases.*.selling_unit_data.*.selling_price'
        ];
    }


    public function messages()
    {
        return [
            // Custom messages if needed
            'purchases.*.price_id.required_without' => 'The price ID is required unless cost price is specified.',
            'purchases.*.cost_price.required_without' => 'The cost price is required unless price ID is specified.',
        ];
    }
}
