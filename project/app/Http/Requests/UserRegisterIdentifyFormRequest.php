<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRegisterIdentifyFormRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'identification_id' => ['required', 'string', 'max:255'],
            'date_of_birth' => ['required', 'date', 'before: today'],
        ];
    }
}
