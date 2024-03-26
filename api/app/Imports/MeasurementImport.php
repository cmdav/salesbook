<?php

namespace App\Imports;

use App\Models\Measurement; 
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Illuminate\Support\Str;
use App\Http\Requests\MeasurementFormRequest; 

class MeasurementImport implements ToModel, WithHeadingRow, WithValidation, SkipsEmptyRows 
{
    public function model(array $row)
    {
       
        $measurementName = isset($row['measurement_name']) ? Str::limit(trim($row['measurement_name']), 30) : null;
        $unit = isset($row['unit']) ? Str::limit(trim($row['unit']), 5) : null;

        if ($measurementName && $unit) {
            return new Measurement([
                'measurement_name' => $measurementName,
                'unit' => $unit,
            ]);
        }

        return null;
    }

    public function rules(): array
    {
        
        $measurementFormRequest = new MeasurementFormRequest();
        return $measurementFormRequest->rules();
    }
}
