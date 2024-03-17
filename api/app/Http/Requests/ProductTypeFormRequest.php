<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


class ProductTypeFormRequest extends FormRequest
{
   

   

    public function rules(Request $request): array
    {
        $productIdRule = 'required|uuid';
        $productTypeRule = [
            'required',
            'string',
            'max:50',
            Rule::unique('product_types')->where(function ($query) use ($request) {
                return $query->where('product_id', $request->product_id);
            })
        ];
    
        if ($this->getMethod() === 'PUT') {
            // When updating, exclude the current product's ID and product type
            $productTypeRule[] = Rule::ignore($this->route('product_type'));
        }
    
        return [
            'product_id' => $productIdRule,
            'product_type' => $productTypeRule,
            'product_type_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'product_type_description' => 'required|string',
            'organization_id' => 'nullable|uuid',
            'supplier_id' => 'nullable|uuid',
        ];
    }
    

  

}
