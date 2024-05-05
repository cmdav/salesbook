<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Price;
use App\Models\Store;
use App\Models\ProductType; // Ensure this model is correctly set up to interact with the 'product_types' table

class SaleFormRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'customer_id' => 'nullable|uuid',
            'payment_method' => 'required|string',
            'products' => 'required|array|min:1',
            'products.*.product_type_id' => 'required|uuid',
            'products.*.batch_no' => 'nullable|string',
            'products.*.price_sold_at' => [
                'required',
                'integer',
                function ($attribute, $value, $fail) {
                    $index = explode('.', $attribute)[1];
                    $productTypeId = $this->input('products')[$index]['product_type_id'];

                    $productType = ProductType::where('id', $productTypeId)->first();
                    if (!$productType) {
                        $fail('Invalid product type ID provided.');
                        return;
                    }

                    $price = Price::where('product_type_id', $productTypeId)
                                  ->where('status', 1) // assuming '1' indicates active
                                  ->first();

                    if (!$price) {
                        $fail('No active price set for product: ' . $productType->product_type_name);
                    } elseif ($value < $price->selling_price) {
                        $fail('The price sold at ('. $value .') cannot be less than the active selling price ('. $price->selling_price .') for product: ' . $productType->product_type_name);
                    }
                },
            ],
            'products.*.quantity' => [
                'required',
                'integer',
                function ($attribute, $value, $fail) {
                    $index = explode('.', $attribute)[1];
                    $productTypeId = $this->input('products')[$index]['product_type_id'];

                    $productType = ProductType::where('id', $productTypeId)->first();
                    if (!$productType) {
                        $fail('Invalid product type ID provided.');
                        return;
                    }

                    $store = Store::where('product_type_id', $productTypeId)->first();
                    
                    if (!$store) {
                        $fail('Store item not found for the product: ' . $productType->product_type_name);
                    } elseif ($store->quantity_available - $value < 0) {
                        $fail('The entered quantity ('. $value .') exceeds the quantity available ('. $store->quantity_available .') for product: ' . $productType->product_type_name);
                    }
                },
            ],
        ];
    }

    public function messages()
    {
        return [
            // Custom error messages can be added here
        ];
    }
}
