<?php

namespace Tests\Unit;

use App\Http\Requests\UserLoginFormRequest;
use App\Http\Requests\UserRegisterFormRequest;
use App\Http\Requests\UserRegisterVerifyFormRequest;
use App\Http\Requests\UserTwoFactorLoginFormRequest;
use App\Http\Requests\UserTwoFactorRegisterFormRequest;
use App\Rules\MobileNumber;
use App\Rules\Password;
use PHPUnit\Framework\TestCase;

class FormRequestValidationTest extends TestCase
{
    public function testUserLoginFormRequestRequiredRules()
    {
        $formRequest = new UserLoginFormRequest();
        $this->assertEquals([
            'username' => ['required'],
            'password' => ['required'],
        ], $formRequest->rules());
    }

    public function testUserRegisterFormRequestRequiredRules()
    {
        $formRequest = new UserRegisterFormRequest();
        $this->assertEquals([
            'username' => ['required', 'alpha_dash', 'min:3', 'max:255', 'unique:App\Models\UserAccount,username'],
            'password' => ['required', 'string', 'min:8', new Password()],
            'password_confirm' => ['required', 'same:password'],
        ], $formRequest->rules());
    }

    public function testUserRegisterVerifyFormRequestRequiredRules()
    {
        $formRequest = new UserRegisterVerifyFormRequest();
        $this->assertEquals([
            'bank_profile_id' => ['required', 'string', 'exists:App\Models\BankProfile,id'],
            'identification_id' => ['required', 'string', 'max:255'],
            'date_of_birth' => ['required', 'date', 'before: today'],
        ], $formRequest->rules());
    }

    public function testUserTwoFactorLoginFormRequestRequiredRules()
    {
        $formRequest = new UserTwoFactorLoginFormRequest();
        $this->assertEquals([
            '2fa_token' => ['required', 'digits:6']
        ], $formRequest->rules());
    }

    public function testUserTwoFactorRegisterFormRequestRequiredRules()
    {
        $formRequest = new UserTwoFactorRegisterFormRequest();
        $this->assertEquals([
            'email_address' => ['required', 'email:rfc'],
            'mobile_number' => ['required', new MobileNumber()]
        ], $formRequest->rules());
    }
}
