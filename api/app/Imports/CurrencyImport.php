<?php

namespace App\Imports;

use App\Models\Currency;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Str;

class CurrencyImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        
       
        $currencyName = isset($row['currency_name']) ? Str::limit(trim($row['currency_name']), 15) : null;
        $currencySymbol = isset($row['currency_symbol']) ? Str::limit(trim($row['currency_symbol']), 5) : null;

       
        if ($currencyName && $currencySymbol) {
            return new Currency([
                'currency_name' => $currencyName,
                'currency_symbol' => $currencySymbol,
            ]);
        }


        return null;
    }

   
}
