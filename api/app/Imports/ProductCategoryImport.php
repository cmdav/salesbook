<?php

namespace App\Imports;

use App\Models\ProductCategory; 
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Illuminate\Support\Str;
use App\Http\Requests\ProductCategoryFormRequest; 

class ProductCategoryImport implements ToModel, WithHeadingRow, WithValidation, SkipsEmptyRows
{
    public function model(array $row)
    {
       
        $categoryName = isset($row['category_name']) ? Str::limit(trim($row['category_name']), 55) : null;
        $categoryDescription = isset($row['category_description']) ? Str::limit(trim($row['category_description']), 200) : null;

        if ($categoryName && $categoryDescription) {
            return new ProductCategory([
                'category_name' => $categoryName,
                'category_description' => $categoryDescription,
            ]);
        }

        return null;
    }

    public function rules(): array
    {
        
        $productCategoryFormRequest = new ProductCategoryFormRequest();
        return $productCategoryFormRequest->rules();
    }
    public function customValidationMessages()
    {
        return [
            'category_name.regex' => 'The category name must not start  with a space.',
            
        ];
    }
}
