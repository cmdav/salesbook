<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class ProductSubcategoryFormRequest extends FormRequest
{
    
    public function rules(Request $request): array
    {
        return [
            'sub_category_name' => 'required|string|max:50|unique:product_sub_categories',
            'category_id' => 'required|uuid',
        
        ];
    }
    

}
