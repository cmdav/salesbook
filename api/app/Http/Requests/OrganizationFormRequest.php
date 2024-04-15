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
        $user_id = $request->route('organization'); 
        return [
            
            // 'organization_name' => [
            //     'required',
            //     'string',
            //     'max:55',
            //     Rule::unique('organizations')->ignore($user_id, 'user_id'), 
            // ],
            // 'organization_type' => 'required|string|in:sole_proprietor,company,sales_personnel',
            // 'organization_url' => 'nullable|string|max:55|url',
            'organization_logo' => $request->isMethod('put') ? 'sometimes|image|mimes:jpeg,png,jpg,gif,svg|max:2048' : 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        
        ];
    }


}
