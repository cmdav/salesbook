<?php

namespace App\Imports;

use App\Models\PurchaseUnit;
use App\Models\SellingUnit;
use App\Models\SellingUnitCapacity;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Illuminate\Support\Str;

class PurchaseUnitImport implements ToModel, WithHeadingRow, WithValidation, SkipsEmptyRows
{
    public function model(array $row)
    {
        // Ensure the row has the necessary fields
        $purchaseUnitName = isset($row['purchase_unit']) ? Str::limit(trim($row['purchase_unit']), 30) : null;
        $sellingUnitName = isset($row['selling_unit']) ? Str::limit(trim($row['selling_unit']), 30) : null;
        $sellingUnitCapacity = isset($row['number_of_pieces']) ? (int) trim($row['number_of_pieces']) : null;
        $piece_name = isset($row['piece_name']) ? Str::limit(trim($row['piece_name']), 50) : null;

        if ($purchaseUnitName && $sellingUnitName && $sellingUnitCapacity && $piece_name) {
            // Find or create the PurchaseUnit
            $purchaseUnit = PurchaseUnit::firstOrCreate(['purchase_unit_name' => $purchaseUnitName]);

            // Find or create the SellingUnit associated with the PurchaseUnit
            $sellingUnit = SellingUnit::firstOrCreate([
                'purchase_unit_id' => $purchaseUnit->id,
                'selling_unit_name' => $sellingUnitName,
            ]);

            // Create the SellingUnitCapacity associated with the SellingUnit
            SellingUnitCapacity::firstOrCreate([
                'selling_unit_id' => $sellingUnit->id,
                'selling_unit_capacity' => $sellingUnitCapacity,
                'piece_name' => $piece_name,
            ]);

            return null;
        }

        return null;
    }

    public function rules(): array
    {
        return [
            'purchase_unit' => 'required|string|max:30',
            'selling_unit' => 'required|string|max:30',
            'number_of_pieces' => 'required|integer|min:1',
            'piece_name' => 'required|string|max:50|unique:selling_unit_capacities,piece_name', // Adjust the field name as necessary
        ];
    }

    public function customValidationMessages()
    {
        return [
            'purchase_unit.required' => 'The purchase unit name is required.',
            'selling_unit.required' => 'The selling unit name is required.',
            'number_of_pieces.required' => 'The selling unit capacity is required.',
            'number_of_pieces.integer' => 'The selling unit capacity must be an integer.',
            'number_of_pieces.min' => 'The selling unit capacity must be at least 1.',
            'piece_name.required' => 'The selling unit display name is required.',
            'piece_name.unique' => 'The selling unit display name must be unique.',
        ];
    }
}
