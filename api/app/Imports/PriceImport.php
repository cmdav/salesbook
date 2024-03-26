<?php

namespace App\Imports;

use App\Models\Price;
use App\Models\ProductType;
use App\Models\Currency;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;

class PriceImport implements ToModel, WithHeadingRow, WithValidation, SkipsEmptyRows
{
    public function model(array $row)
    {
        $productType = ProductType::where('product_type_name', trim($row['product_type_name']))->first();
        $currency = Currency::where('currency_name', trim($row['currency_name']))->first();
        
        if (!$productType || !$currency) {
            return null; 
        }

        return new Price([
            'product_type_id' => $productType->id,
            'currency_id' => $currency->id,
            'cost_price' => $row['cost_price'],
            'selling_price' => $row['selling_price'],
            'auto_generated_selling_price' => $row['auto_generated_selling_price'] ?? null,
            'discount' => $row['discount'] ?? null,
          
        ]);
    }

    public function rules(): array
    {
        return [
            'product_type_name' => 'required|string|exists:product_types,product_type_name',
            'currency_name' => 'required|string|exists:currencies,currency_name',
            'cost_price' => 'required|integer',
            'selling_price' => 'required|integer',
            'auto_generated_selling_price' => 'nullable|integer|between:0,100',
            'discount' => 'nullable|integer',
           
        ];
    }

    public function customValidationMessages()
    {
        return [
            'product_type_name.exists' => 'The specified product type name does not exist.',
            'currency_name.exists' => 'The specified currency name does not exist.',
            // Add other custom messages as necessary
        ];
    }
}
