<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class ProductFormRequest extends FormRequest
{
    
    public function rules(Request $request = Null): array
    {
        return [

            'product_name' => 'required|string|max:50|unique:products',
            'product_description' => 'required|string|max:200',
            'product_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'measurement_id' => 'required|uuid|max:40',
            'category_id' => 'required|uuid|max:40',
            'sub_category_id' => 'required|uuid|max:40',
        
        ];
    }
   

}
