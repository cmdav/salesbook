<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class MeasurementFormRequest extends FormRequest
{
    
    public function rules(Request $request): array
    {
        return [
           
            'measurement_name' => 'required|string|max:30|unique:measurements',
            'unit' => 'required|string|max:5|unique:measurements',
        
        ];
    }
   

}
