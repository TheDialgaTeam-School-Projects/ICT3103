<?php

namespace App\Http\Requests;

use App\Rules\Password;
use Illuminate\Foundation\Http\FormRequest;

class UserRegisterFormRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'date_of_birth' => ['required', 'date', 'before: today'],
            'username' => ['required', 'alpha_dash', 'min:3', 'max:255', 'unique:App\Models\User,username'],
            'password' => ['required', 'string', 'min:8', new Password()],
            'password_confirm' => ['required', 'same:password'],
        ];
    }
}
