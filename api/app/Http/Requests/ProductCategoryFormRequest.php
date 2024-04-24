<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductCategoryFormRequest extends FormRequest
{
    public function rules(): array
    {
        
        $productCategoryId = $this->route('product_category'); 

        return [
            'category_name' => [
                'required',
                'string',
                'max:55',
                'regex:/^[^\s]/',
                Rule::unique('product_categories')->ignore($productCategoryId) 
            ],
            'category_description' => [
                'required',
                'string',
                'max:200'
            ],
        ];
    }
}
