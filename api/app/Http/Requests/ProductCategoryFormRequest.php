<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class ProductCategoryFormRequest extends FormRequest
{
    
    public function rules(Request $request = Null): array
    {
        return [
            
            'category_name' => 'required|string|max:55|unique:product_categories|regex:/^[^\s]/',
            'category_description' => 'required|string|max:200',
           
        
        ];
    }
   

}
