<?php

namespace App\Exports;

use App\Models\Price;
use Maatwebsite\Excel\Concerns\FromCollection;

class PriceExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Price::all();
    }
}
