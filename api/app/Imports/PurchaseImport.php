<?php

namespace App\Imports;

use App\Models\Purchase;
use App\Models\ProductType;
use App\Models\User; 
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;

class PurchaseImport implements ToModel, WithHeadingRow, WithValidation, SkipsEmptyRows
{
    public function model(array $row)
    {
        $productType = ProductType::where('product_type_name', trim($row['product_type_name']))->first();
       // $supplier = User::where('first_name', trim($row['supplier_fullname']))->first(); 
        
        if (!$productType) {
            return null; 
        }

        return new Purchase([
            'product_type_id' => $productType->id,
            //'supplier_id' => $supplier ? $supplier->id : null,
            'price' => $row['price'],
            'batch_no' => $row['batch_no'],
            'quantity' => $row['quantity'],
            'product_identifier' => $row['product_identifier'] ?? null, 
            'expired_date' => $row['expired_date'] ?? null, 
          
        ]);
    }

    public function rules(): array
    {
        return [
            'product_type_name' => 'required|string|exists:product_types,product_type_name',
            'supplier_fullname' => 'nullable|string|exists:users,name',
            'price' => 'required|integer',
            'batch_no' => 'required|string|max:50',
            'quantity' => 'required|integer',
            'product_identifier' => 'nullable|string|max:50',
            'expired_date' => 'nullable|date',
            // Remove 'purchase_by' from rules if it's automatically set
        ];
    }

    public function customValidationMessages()
    {
        return [
            'product_type_name.exists' => 'The specified product type name does not exist.',
            'supplier_fullname.exists' => 'The specified supplier name does not exist.',
            // Add other custom messages as needed
        ];
    }
}
