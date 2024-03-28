<?php

namespace App\Exports;

use App\Models\ProductSubCategory;
use Maatwebsite\Excel\Concerns\FromCollection;

class ProductSubCategoryExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return ProductSubCategory::all();
    }
}
