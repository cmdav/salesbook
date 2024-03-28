<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use App\Models\Store;

class SaleFormRequest extends FormRequest
{
    public function rules(Request $request): array
    {
        return [
            'product_type_id' => 'required|uuid',
            'customer_id' => 'nullable|uuid',
            'price_sold_at' => 'required|integer',
            'quantity' => [
                'required',
                'integer',
                function ($attribute, $value, $fail) use ($request) {
                    $store = Store::where('product_type_id', $request->product_type_id)->first();

                    if (!$store) {
                        $fail('Store item not found for the specified product type.');
                    } else if ($store->quantity_available - $value < 0) {
                        $fail('Insufficient store items.');
                    }
                },
            ],
            'payment_method' => 'required|string',
            // 'sales_owner' => 'required|uuid',
        ];
    }

    public function messages()
    {
        return [
            // Your existing messages
        ];
    }
}
