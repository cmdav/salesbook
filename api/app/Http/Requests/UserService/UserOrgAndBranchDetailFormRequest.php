<?php

namespace App\Http\Requests\UserService;

use Illuminate\Foundation\Http\FormRequest;

class UserOrgAndBranchDetailFormRequest extends FormRequest
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