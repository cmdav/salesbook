<?php

namespace App\Imports;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductSubcategory;
use App\Models\Measurement;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Illuminate\Support\Str;

class ProductImport implements ToModel, WithHeadingRow, WithValidation, SkipsEmptyRows
{
    public function model(array $row)
    {
        $category = ProductCategory::where('category_name', trim($row['category_name']))->first();
        $subCategory = ProductSubcategory::where('sub_category_name', trim($row['sub_category_name']))->first();
        $measurement = Measurement::where('measurement_name', trim($row['measurement_name']))->first();

        if (!$category || !$subCategory || !$measurement) {
            return null;
        }

        return new Product([
            'product_name' => Str::limit(trim($row['product_name']), 50),
            'product_description' => Str::limit(trim($row['product_description']), 200),
            'measurement_id' => $measurement->id,
            'category_id' => $category->id,
            'sub_category_id' => $subCategory->id,
           
        ]);
    }

    public function rules(): array
    {
        return [
            'product_name' => 'required|string|max:50|unique:products|regex:/^[^\s]/',
            'product_description' => 'required|string|max:200',
            'measurement_name' => 'required|string|exists:measurements,measurement_name',
            'category_name' => 'required|string|exists:product_categories,category_name',
            'sub_category_name' => 'required|string|exists:product_sub_categories,sub_category_name',
           
        ];
    }

    public function customValidationMessages()
    {
        return [
            'category_name.exists' => 'The specified product category does not exist.',
            'sub_category_name.exists' => 'The specified product subcategory does not exist.',
            'measurement_name.exists' => 'The specified measurement does not exist.',
            'category_name.regex' => 'The category name must not start or end with a space.',
            'product_name.regex' => 'The category name must not start with a space.',
        ];
    }
}
