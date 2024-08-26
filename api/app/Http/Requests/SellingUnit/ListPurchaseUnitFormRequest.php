<?php

namespace App\Http\Requests\SellingUnit;

use Illuminate\Foundation\Http\FormRequest;

class ListPurchaseUnitFormRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            // Validation rules
        ];
    }
}