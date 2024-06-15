<?php

namespace App\Http\Requests\Security;

use Illuminate\Foundation\Http\FormRequest;

class SubscriptionStatusFormRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'plan_id' => 'required|exists:subscriptions,id|integer',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after_or_equal:start_time',
            'organization_id' => 'required|uuid|exists:organizations,id',
        ];
    }
}