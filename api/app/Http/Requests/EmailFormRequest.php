<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class EmailFormRequest extends FormRequest
{
    public function rules(Request $request): array
    {
        $rules = [

            'email' => 'required|email',

            'type' => 'required|in:reset-password,resend,invitation',
        ];

        if ($request->input('type') === 'invitation') {

            $rules['organization_id'] = 'nullable|uuid|exists:organizations,id';
            $rules['first_name'] = 'required|string';


        } elseif ($request->input('type') !== 'invitation') {

            $rules['email'] .= '|exists:users';
        }

        return $rules;
    }


}
