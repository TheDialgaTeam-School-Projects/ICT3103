<?php

namespace Tests\Unit;

use App\Http\Requests\BankAccountTransferConfirmFormRequest;
use App\Http\Requests\BankAccountTransferFormRequest;
use App\Http\Requests\UserLoginFormRequest;
use App\Http\Requests\UserRegisterCreateFormRequest;
use App\Http\Requests\UserRegisterIdentifyFormRequest;
use App\Http\Requests\UserRegisterVerifyFormRequest;
use App\Http\Requests\UserTwoFactorLoginFormRequest;
use App\Rules\Password;
use PHPUnit\Framework\TestCase;

class FormRequestValidationTest extends TestCase
{
    public function testBankAccountTransferConfirmFormRequestRequiredRules()
    {
        $formRequest = new BankAccountTransferConfirmFormRequest();
        $this->assertEquals([
            'two_factor_token' => ['required', 'digits:6'],
        ], $formRequest->rules());
    }

    public function testBankAccountTransferFormRequestRequiredRules()
    {
        $formRequest = new BankAccountTransferFormRequest();
        $this->assertEquals([
            'bank_account_id_to' => ['required', 'string', 'max:255', 'exists:App\Models\BankAccount,id'],
            'amount' => ['required', 'regex:/(?:\\d+\\.\\d{1,2}|0\\.\\d{1,2}|\\d+)/'],
        ], $formRequest->rules());
    }

    public function testUserLoginFormRequestRequiredRules()
    {
        $formRequest = new UserLoginFormRequest();
        $this->assertEquals([
            'username' => ['required'],
            'password' => ['required'],
        ], $formRequest->rules());
    }

    public function testUserRegisterCreateFormRequestRequiredRules()
    {
        $formRequest = new UserRegisterCreateFormRequest();
        $this->assertEquals([
            'username' => ['required', 'alpha_dash', 'min:3', 'max:255', 'unique:App\Models\UserAccount,username'],
            'password' => ['required', 'string', 'min:8', new Password()],
            'password_confirm' => ['required', 'same:password'],
        ], $formRequest->rules());
    }

    public function testUserRegisterIdentifyFormRequestRequiredRules()
    {
        $formRequest = new UserRegisterIdentifyFormRequest();
        $this->assertEquals([
            'identification_id' => ['required', 'string', 'max:255'],
            'date_of_birth' => ['required', 'date', 'before: today'],
        ], $formRequest->rules());
    }

    public function testUserRegisterVerifyFormRequestRequiredRules()
    {
        $formRequest = new UserRegisterVerifyFormRequest();
        $this->assertEquals([
            'two_factor_token' => ['required', 'digits:6'],
        ], $formRequest->rules());
    }

    public function testUserTwoFactorLoginFormRequestRequiredRules()
    {
        $formRequest = new UserTwoFactorLoginFormRequest();
        $this->assertEquals([
            'two_factor_token' => ['required', 'digits:6'],
        ], $formRequest->rules());
    }
}
