<?php

namespace App\Http\Requests\Security;

use Illuminate\Foundation\Http\FormRequest;

class SubscriptionFormRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = [
            'plan_name' => 'required|string|max:50|unique:subscriptions,plan_name',
            'description' => 'required|string',
        ];

        if ($this->isMethod('put') || $this->isMethod('patch')) {
            $planId = $this->route('subscriptions'); 
            $rules['plan_name'] .= ',' . $planId;
        }

        return $rules;
    }
}