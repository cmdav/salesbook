<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use App\Models\Store;
use App\Models\Price; // Make sure to import your Price model

class SaleFormRequest extends FormRequest
{
    public function rules(Request $request): array
    {
        return [
            'product_type_id' => 'required|uuid',
            'customer_id' => 'nullable|uuid',
            'price_sold_at' => [
                'required',
                'integer',
                function ($attribute, $value, $fail) use ($request) {
                    // Retrieve the selling price for the given product type where status is 1
                    $price = Price::where('product_type_id', $request->product_type_id)
                        ->where('status', 1)
                        ->first();
                        
                    if (!$price) {
                        $fail('Selling price not found for the specified product type.');
                    } elseif ($value < $price->selling_price) {
                        $fail('The price sold at ('. $value .') cannot be less than the selling price ('. $price->selling_price .').');
                    }
                },
            ],
            'quantity' => [
                'required',
                'integer',
                function ($attribute, $value, $fail) use ($request) {
                    $store = Store::where('product_type_id', $request->product_type_id)->first();
    
                    if (!$store) {
                        $fail('Store item not found for the specified product type.');
                    } elseif ($store->quantity_available - $value < 0) {
                        $fail('The entered quantity ('. $value .') is more than what is available ('. $store->quantity_available .').');
                    }
                },
            ],
            'payment_method' => 'required|string',
        ];
    }

    public function messages()
    {
        return [
            // Your existing messages
        ];
    }
}
