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
           // 'purchases.*.container_capacity_id' => 'required',
            //'purchases.*.container_qty' => 'required|integer',
            'purchases.*.capacity_qty' => 'nullable|integer',
            'purchases.*.price_id' => 'required_without:purchases.*.cost_price,purchases.*.selling_price',
            'purchases.*.cost_price' => 'required_without:purchases.*.price_id',
            'purchases.*.selling_price' => 'required_without:purchases.*.price_id',
            'purchases.*.batch_no' => 'required|string|max:50',
            'purchases.*.product_identifier' => 'nullable|string|max:50',
            'purchases.*.expiry_date' => [
                'nullable',
                'date',
                'after_or_equal:today'
            ],
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
