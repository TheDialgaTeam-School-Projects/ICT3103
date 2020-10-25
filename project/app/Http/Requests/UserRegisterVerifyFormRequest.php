<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRegisterVerifyFormRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'bank_profile_id' => ['required', 'string', 'exists:App\Models\BankProfile,id'],
            'identification_id' => ['required', 'string', 'max:255'],
            'date_of_birth' => ['required', 'date', 'before: today'],
        ];
    }
}
