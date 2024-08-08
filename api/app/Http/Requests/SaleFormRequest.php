<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Price;
use App\Models\Store;
use App\Models\ProductType;

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
                'required',
                'integer',
               
            ],
            'products.*.capacity_qty' => [
                'required',
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
            // Custom validation messages can be added here
        ];
    }
}
