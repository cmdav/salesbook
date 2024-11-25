<?php

namespace App\Imports;

use App\Models\Product;
use App\Models\ProductType;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Illuminate\Support\Str;

class ProductTypeImport implements ToModel, WithHeadingRow, WithValidation, SkipsEmptyRows
{
    public function model(array $row)
    {
        $product = Product::where('product_name', trim($row['product_name']))->first();

        if (!$product) {
            return null;
        }

        return new ProductType([
            'product_type_name' => Str::limit(trim($row['product_type_name']), 200),
            'product_type_description' => Str::limit(trim($row['product_type_description']), 200),
            'product_id' => $product->id,
    
           
        ]);
    }

    public function rules(): array
    {
        return [
            'product_type_name' => 'required|string|max:200|unique:product_types|regex:/^[^\s]/',
            'product_type_description' => 'required|string|max:200',
           
           
        ];
    }

    public function customValidationMessages()
    {
        return [
            'product_name.exists' => 'The specified product name does not exist.',
            'product_type_name.regex' => 'The category name must not start  with a space.',
           
        ];
    }
}
