<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class TaskFormRequest extends FormRequest
{
    
    public function rules(Request $request): array
    {
        return [
            'name'=>'required|max:50',
            'description' => 'required|string|max:200',
            'start_time' => 'required|date',
            'end_time' => 'required|date',
        
        ];
    }


}
