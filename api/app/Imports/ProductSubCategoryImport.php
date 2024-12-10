<?php

namespace App\Imports;

use App\Models\ProductSubCategory;
use App\Models\ProductCategory;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\ProductSubcategoryFormRequest;

class ProductSubcategoryImport implements ToModel, WithHeadingRow, WithValidation, SkipsEmptyRows
{
    private $validationErrors = [];



    public function model(array $row)
    {
        // Validate and fetch the category name

        $category = ProductCategory::where('category_name', trim($row['category_name']))->first();

        if (!$category) {
            // If category does not exist, record the error and skip the model creation
            $this->validationErrors[] = ['Category does not exist for ' => $row['category_name']];
            return null;
        }

        $subCategoryName = isset($row['sub_category_name']) ? Str::limit(trim($row['sub_category_name']), 50) : null;
        $subCategoryDescription = isset($row['sub_category_description']) ? Str::limit(trim($row['sub_category_description']), 200) : null;

        if ($subCategoryName && $subCategoryDescription) {
            return new ProductSubcategory([
                'sub_category_name' => $subCategoryName,
                'category_id' => $category->id, // Use the found category ID
                'sub_category_description' => $subCategoryDescription,
            ]);
        }

        return null;
    }

    public function rules(): array
    {

        return [
            'category_name' => 'required|string|exists:product_categories,category_name',
            'sub_category_name' => 'required|string|max:50|unique:product_sub_categories',
            'sub_category_description' => 'required|string|max:200',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'category_name.exists' => 'The specified category does not exist.',
            'sub_category_name.regex' => 'The sub category name must not start  with a space.',
        ];
    }

    public function getValidationErrors()
    {
        return $this->validationErrors;
    }
}
