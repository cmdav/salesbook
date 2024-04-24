<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductSubcategoryFormRequest extends FormRequest
{
    public function rules(): array
    {
       
        $subCategoryId = $this->route('product_sub_category'); 
    

        return [
            'sub_category_name' => [
                'required',
                'string',
                'max:50',
                'regex:/^[^\s]/',
                Rule::unique('product_sub_categories')
                    ->where(function ($query) {
                        return $query->where('category_id', $this->category_id);
                    })
                    ->ignore($subCategoryId), 
            ],
            'category_id' => 'required|string',
            'sub_category_description' => 'required|string|max:200',
        ];
    }
}
