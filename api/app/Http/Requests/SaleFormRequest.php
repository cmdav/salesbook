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
            'products.*.batch_no' => 'nullable|string',
            'products.*.price_sold_at' => [
                'required',
                'integer',
                function ($attribute, $value, $fail) {
                    $index = explode('.', $attribute)[1];
                    $productTypeId = $this->input('products')[$index]['product_type_id'];
                    $batchNo = $this->input('products')[$index]['batch_no'];

                    // Extract the real batch number
                    $realBatchNo = explode('->', $batchNo)[0];

                    $price = Price::where('product_type_id', $productTypeId)
                                  ->where('batch_no', $realBatchNo)
                                  ->first();

                    if (!$price) {
                        $fail('No active price set for the specified batch of the product.');
                    } elseif ($value < $price->selling_price) {
                        $fail('The price sold at ('. $value .') cannot be less than the active selling price ('. $price->selling_price .') 
                        for the specified batch of the product.');
                    }
                },
            ],
            'products.*.quantity' => [
                'required',
                'integer',
                function ($attribute, $value, $fail) {
                    $index = explode('.', $attribute)[1];
                    $productTypeId = $this->input('products')[$index]['product_type_id'];
                    $batchNo = $this->input('products')[$index]['batch_no'];

                    // Extract the real batch number
                    $realBatchNo = explode('->', $batchNo)[0];
                    
                    $store = Store::where('product_type_id', $productTypeId)
                                  ->where('batch_no', $realBatchNo)
                                  ->first();

                    if (!$store) {
                        $fail('Store item not found for the specified batch of the product.');
                    } elseif ($store->quantity_available - $value < 0) {
                        $fail('The entered quantity ('. $value .') exceeds the quantity available ('. $store->quantity_available .')
                         for the specified batch of the product.');
                    }
                },
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
