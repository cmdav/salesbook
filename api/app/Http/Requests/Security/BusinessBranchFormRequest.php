<?php

namespace App\Http\Requests\Security;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BusinessBranchFormRequest extends FormRequest
{
    public function rules()
    {
        $businessBranchId = $this->route('business_branch'); // Get the current business branch ID
        //dd( $businessBranchId);

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('business_branches')->ignore($businessBranchId),
            ],
            'state_id' => 'required|integer',
            'postal_code' => 'required|string|max:20',
            'city' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'country_id' => 'required|integer',
            'contact_person' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                //Rule::unique('business_branches')->ignore($businessBranchId),
            ],
        ];
    }

}
