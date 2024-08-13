<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SaleFormRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'customer_id' => 'nullable|uuid',
            'payment_method' => 'required|string',
            'products' => 'required|array|min:1',
            'products.*.product_type_id' => 'required|uuid',
            'products.*.price_sold_at' => [
                'required',
                'integer',
            ],
            'products.*.container_qty' => [
                'required_without:products.*.capacity_qty',
                'integer',
            ],
            'products.*.capacity_qty' => [
                'required_without:products.*.container_qty',
                'integer',
            ],
            'products.*.vat' => [
                'required',
                'integer',
            ],
        ];
    }

    public function messages()
    {
        return [
            'products.*.container_qty.required_without' => 'The container quantity is required when capacity quantity is not provided.',
            'products.*.capacity_qty.required_without' => 'The capacity quantity is required when container quantity is not provided.',
        ];
    }
}
