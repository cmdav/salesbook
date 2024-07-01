<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use DB;

class UserFormRequest extends FormRequest
{
    public function rules(Request $request): array
    {
        $rules = [
            
            'password' => [
                'required',
                'string',
                'min:8',
                'max:30',
                'confirmed',
                'regex:/[a-z]/',      // must contain at least one lowercase letter
                'regex:/[A-Z]/',      // must contain at least one uppercase letter
                'regex:/[0-9]/',      // must contain at least one digit
                'regex:/[@$!%*#?&]/', // must contain a special character
            ],
            'organization_type' => [
                'required',
                'string',
                'in:sole_properietor,company,sales_personnel,supplier'
            ],
            'phone_number' => 'required|string',
        ];

        if ($request->input('organization_type') == 'company') {
            $rules = array_merge($rules, [
                'email' => ['required', 'email', 'max:55', Rule::unique('users')->ignore($this->user)],
                'company_name' => 'required|string|max:55',
                'company_address' => 'required|string|max:55',
                'contact_person' => 'required|string|max:55',
                'dob' => 'nullable|date|date_format:Y-m-d',
            ]);
        }

        if ($request->input('organization_type') == 'sole_properietor') {
            $rules = array_merge($rules, [
                'first_name' => 'required|string|max:55',
                'last_name' => 'required|string|max:55',
                'middle_name' => 'nullable|string|max:55',
                'dob' => 'nullable|date|date_format:Y-m-d',
                'email' => ['required', 'email', 'max:55', Rule::unique('users')->ignore($this->user)],
            ]);
        }

        // if ($request->input('organization_type') == 'sales_personnel') {
        //     $rules = array_merge($rules, [
        //         'first_name' => 'required|string|max:55',
        //         'last_name' => 'required|string|max:55',
        //         'branch_id' => 'required|integer',
        //         'organization_code' => 'required|string|max:55',
        //         'email' => ['required', 'email', 'max:55', Rule::unique('users')->ignore($this->user)],
        //     ]);
        // }
        if ($request->input('organization_type') == 'supplier') {
            $rules = array_merge($rules, [
                'first_name' => 'required|string|max:55',
                'last_name' => 'required|string|max:55',
                'organization_id' => 'required|string|max:55',
                'email' => [
                    'required',
                    'email',
                    'max:55',
                    function ($attribute, $value, $fail) use ($request) {
                        $exists = DB::table('users')
                            ->where('email', $value)
                            ->where('organization_id', $request->input('organization_id'))
                            ->exists();

                        if (!$exists) {
                            $fail('The email and organization ID combination does not exist.');
                        }
                    }
                ],
                'middle_name' => 'nullable|string|max:55',
                'dob' => 'nullable|date|date_format:Y-m-d',
            ]);
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'password.regex' => 'The :attribute must include at least one uppercase letter, 
            one lowercase letter, one number, and one special character. Your password should be 8 to 30 characters long.',
        ];
    }
}
