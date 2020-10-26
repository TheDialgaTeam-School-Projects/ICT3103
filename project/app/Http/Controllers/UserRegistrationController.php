<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRegisterFormRequest;
use App\Http\Requests\UserRegisterVerifyFormRequest;
use App\Http\Requests\UserTwoFactorRegisterFormRequest;
use App\Repository\UserRepositoryInterface;
use Authy\AuthyApi;
use Illuminate\Http\Request;

class UserRegistrationController extends Controller
{
    public function register_verify_get(Request $request)
    {
        $data = [];
        $this->includeAlertMessage($request, $data);
        return view('user_register_verify', $data);
    }

    public function register_verify_post(UserRegisterVerifyFormRequest $request, UserRepositoryInterface $userRepository)
    {
        if ($this->isServingGlobalTimeout($request, 'user_register_verify')) {
            // User is serving a global timeout for trying to brute force the verification process.
            return view('user_register_verify', [
                'alertType' => 'error',
                'alertMessage' => 'Account registration has been disabled due to mass failed attempt. Please try again later.',
            ]);
        }

        $validated = $request->validated();

        if ($userRepository->isUserAccountCreated($validated['bank_profile_id'])) {
            // User has already created a bank profile with this bank profile id.
            return view('user_register_verify', [
                'alertType' => 'error',
                'alertMessage' => 'User has already created an account with this bank profile.',
            ]);
        }

        if (!$userRepository->isBankProfileValid($validated['bank_profile_id'], $validated['identification_id'], $validated['date_of_birth'])) {
            // User seem to have failed the verification check on the bank profile, this is probably because of typo or trying to brute force their way through.
            $this->incrementGlobalFailedCount($request, 'user_register_verify');

            return view('user_register_verify', [
                'alertType' => 'error',
                'alertMessage' => 'Identification id and Date of Birth must match the registered Bank Profile Id.',
            ]);
        }

        // User has successfully validated the bank profile and should now bring you to register page.
        $this->resetGlobalFailedCount($request, 'user_register_verify');
        $request->session()->put('bank_profile_id', $validated['bank_profile_id']);
        return redirect()->route('user_registration.register_create_get');
    }

    public function register_create_get()
    {
        return view('user_register');
    }

    public function register_create_post(UserRegisterFormRequest $request, UserRepositoryInterface $userRepository)
    {
        if (!$request->session()->has('bank_profile_id')) {
            // Invalid session, maybe the user take too long to complete hence the session variable is gone.
            $this->flashAlertMessage($request, 'error', 'Session has expired. Please try again.');
            return redirect()->route('user_registration.register_verify_get');
        }

        $validated = $request->validated();

        $userRepository->createUserAccount(
            $validated['username'],
            $validated['password'],
            $request->session()->pull('bank_profile_id')
        );

        $this->flashAlertMessage($request, 'success', 'User has been successfully created!');
        return redirect()->route('user_authentication.login_get');
    }

    public function register_2fa_get(Request $request)
    {
        $data = [];
        $this->includeAlertMessage($request, $data);
        return view('user_register_2fa', $data);
    }

    public function register_2fa_post(UserTwoFactorRegisterFormRequest $request, UserRepositoryInterface $userRepository, AuthyApi $authyApi)
    {
        $validated = $request->validated();
        $authyUser = $authyApi->registerUser($validated['email_address'], $validated['mobile_number'], 65);

        if (!$authyUser->ok()) {
            // Invalid 2FA credentials used to register this account. Sorry :P
            return view('user_register_2fa', [
                'alertType' => 'error',
                'alertMessage' => 'Email or Mobile number is invalid.',
            ]);
        }

        // Register the 2FA credentials and verify...?
        if (!$userRepository->isOtpRegistered()) {
            $userRepository->registerBankProfileOtp($authyUser->id());
        } else {
            $userRepository->updateBankProfileOtp($authyUser->id());
        }

        return redirect()->route('user_authentication.login_2fa_get');
    }
}
