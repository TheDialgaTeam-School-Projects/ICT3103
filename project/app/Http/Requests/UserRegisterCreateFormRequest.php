<?php

namespace App\Http\Requests;

use App\Rules\Password;
use Illuminate\Foundation\Http\FormRequest;

class UserRegisterCreateFormRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'username' => ['required', 'alpha_dash', 'min:3', 'max:255', 'unique:App\Models\UserAccount,username'],
            'password' => ['required', 'string', 'min:8', new Password()],
            'password_confirm' => ['required', 'same:password'],
        ];
    }
}
