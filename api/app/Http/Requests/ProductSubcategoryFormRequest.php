<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class ProductSubcategoryFormRequest extends FormRequest
{
    
    public function rules(Request $request = Null): array
    {
        return [
            'sub_category_name' => [
                'required',
                'string',
                'max:50',
                Rule::unique('product_sub_categories')->where(function ($query) use ($request) {
                    return $query->where('category_id', $request->category_id);
                }),
            ],
            'category_id' => 'required|uuid',
            'sub_category_description' => 'required|string|max:200',
        ];
        
    }
    

}
