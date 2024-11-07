<?php

namespace App\Imports;

use App\Models\ProductType;
use App\Models\ProductCategory;
use App\Models\ProductSubCategory;
use App\Models\SellingUnitCapacity;
use App\Models\SellingUnit;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Illuminate\Support\Str;

class ProductImport implements ToModel, WithHeadingRow, WithValidation, SkipsEmptyRows
{
    public function model(array $row)
    {
        // Retrieve the category and subcategory based on names
        $category = ProductCategory::where('category_name', trim($row['category_name']))->first();
        $subCategory = ProductSubCategory::where('sub_category_name', trim($row['sub_category_name']))->first();

        // Retrieve the selling unit capacity based on piece_name
        $sellingUnitCapacity = SellingUnitCapacity::where('piece_name', trim($row['piece_name']))->first();

        if (!$category || !$subCategory || !$sellingUnitCapacity) {
            return null;
        }

        // Retrieve the selling unit and purchase unit
        $sellingUnit = $sellingUnitCapacity->sellingUnit;
        $purchaseUnit = $sellingUnit->purchaseUnit;

        // Convert VAT value to 0 or 1
        $vatValue = strtolower(trim($row['vat'])) === 'yes' ? 1 : 0;

        $productType = null;

        DB::transaction(function () use ($row, $category, $subCategory, $sellingUnitCapacity, $sellingUnit, $purchaseUnit, $vatValue) {
            $productType = new ProductType([
                'product_type_name' => Str::limit(trim($row['product_type_name']), 50),
                'product_type_description' => Str::limit(trim($row['product_type_description']), 200),
                'product_type_image' => null, // You might need to handle image uploading separately
                'vat' => $vatValue,
                'sub_category_id' => $subCategory->id,
                'category_id' => $category->id,
                'selling_unit_capacity_id' => $sellingUnitCapacity->id,
                'selling_unit_id' => $sellingUnit->id,
                'purchase_unit_id' => $purchaseUnit->id,
                'barcode' => Str::limit(trim($row['barcode']), 200),
                // 'created_by' and 'updated_by' fields should be set based on your application logic
                // 'created_by' => ?,
                // 'updated_by' => ?,
            ]);
            $productType->save();
        });

        return $productType;
    }

    public function rules(): array
    {
        return [
            'product_type_name' => 'required|string|max:50|unique:product_types|regex:/^[^\s]/',
            'product_type_description' => 'required|string|max:200',
            'piece_name' => 'required|string|exists:selling_unit_capacities,piece_name',
            'category_name' => 'required|string|exists:product_categories,category_name',
            'sub_category_name' => 'required|string|exists:product_sub_categories,sub_category_name',
            'vat' => 'required|string|in:yes,no',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'category_name.exists' => 'The specified product category does not exist.',
            'sub_category_name.exists' => 'The specified product subcategory does not exist.',
            'piece_name.exists' => 'The specified piece name does not exist in the selling unit capacities.',
            'vat.in' => 'The VAT field must be either "yes" or "no".',
        ];
    }
}
