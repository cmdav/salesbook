<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class OrganizationFormRequest extends FormRequest
{
    
    public function rules(Request $request): array
    {
        return [
            
            'organization_name' => 'required|string|max:55|unique:organizations',
            'organization_url' => 'nullable|string|max:55|url',
            'organization_logo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        
        ];
    }


}
