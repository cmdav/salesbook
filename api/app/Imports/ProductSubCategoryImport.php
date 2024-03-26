<?php

namespace App\Imports;

use App\Models\ProductSubCategory;
use Maatwebsite\Excel\Concerns\ToModel;

class ProductSubCategoryImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new ProductSubCategory([
            //
        ]);
    }
}
