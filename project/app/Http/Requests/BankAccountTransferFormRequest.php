<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class BankAccountTransferFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'bank_account_id_to' => ['required', 'string', 'max:255', 'exists:App\Models\BankAccount,id'],
            'amount' => ['required', 'regex:/(?:\\d+\\.\\d{1,2}|0\\.\\d{1,2}|\\d+)/'],
        ];
    }
}
