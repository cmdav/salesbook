<?php

namespace App\Imports;

use App\Models\Sale;
use App\Models\ProductType;
use App\Models\User; 
use App\Models\Price; 
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Illuminate\Support\Facades\Validator;

class SaleImport implements ToModel, WithHeadingRow, WithValidation, SkipsEmptyRows
{
    public function model(array $row)
    {
        $productType = ProductType::where('product_type_name', trim($row['product_type_name']))->first();
        $customer = User::where('first_name', trim($row['customer_fullname']))->first(); 
        $price = Price::where([['product_type_id',$productType->id],['status','active']])->first(); 
        if (!$productType || !$price) {
            return null; 
        }

       
        return new Sale([
            'product_type_id' => $productType->id,
            'customer_id' => $customer ? $customer->id : null, 
            'price_sold_at' => $row['price_sold_at'],
            'quantity' => $row['quantity'],
            'price_id' => $price->id,
            'payment_method' => $row['payment_method'],
            
        ]);
    }

    public function rules(): array
    {
        return [
            'product_type_name' => 'required|string|exists:product_types,product_type_name',
            'customer_fullname' => 'nullable|string|exists:users,name',
            'price_sold_at' => 'required|integer',
            'quantity' => 'required|integer',
            'payment_method' => 'required|string|in:cash,Pos,Bank Transfer',
           
        ];
    }

    public function customValidationMessages()
    {
        return [
            'product_type_name.exists' => 'The specified product type name does not exist.',
            'customer_fullname.exists' => 'The specified customer name does not exist.',
            
        ];
    }
}
