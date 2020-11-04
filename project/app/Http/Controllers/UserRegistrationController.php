<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRegisterCreateFormRequest;
use App\Http\Requests\UserRegisterIdentifyFormRequest;
use App\Http\Requests\UserRegisterVerifyFormRequest;
use App\Models\BankProfile;
use App\Models\UserAccount;
use App\Services\AuthyService;
use Illuminate\Http\Request;

class UserRegistrationController extends Controller
{
    private const REGISTER_IDENTIFY_VIEW = 'user_register_identify';
    private const REGISTER_VERIFY_VIEW = 'user_register_verify';
    private const REGISTER_CREATE_VIEW = 'user_register_create';

    public function register_identify_get()
    {
        return $this->view(self::REGISTER_IDENTIFY_VIEW);
    }

    public function register_identify_post(UserRegisterIdentifyFormRequest $request, BankProfile $bankProfile)
    {
        return $this->getGlobalLockoutViewOrContinue(self::REGISTER_IDENTIFY_VIEW, function () use ($request, $bankProfile) {
            $formInputs = $request->validated();

            if (!$bankProfile->isValidBankProfile($formInputs['identification_id'], $formInputs['date_of_birth'])) {
                // User seem to have failed the identification check on the bank profile, this is probably because of typo or trying to brute force their way through.
                $this->incrementGlobalLockoutFailedCount(self::REGISTER_IDENTIFY_VIEW);
                $this->flashAlertMessage('error', $this->__('registration.user_identify_failed'));
                return $this->view(self::REGISTER_IDENTIFY_VIEW);
            }

            // User is identified.
            $bankProfileId = $bankProfile->getBankProfileId($formInputs['identification_id']);

            if ($bankProfile->isUserAccountCreated($bankProfileId)) {
                // User seem to have owned an account.
                $this->incrementGlobalLockoutFailedCount(self::REGISTER_IDENTIFY_VIEW);
                $this->flashAlertMessage('error', $this->__('registration.user_identify_user_has_account'));
                return $this->view(self::REGISTER_IDENTIFY_VIEW);
            }

            // User does not own an account, proceed to verification.
            $this->resetGlobalLockoutFailedCount(self::REGISTER_IDENTIFY_VIEW);
            $this->getSession()->put('bank_profile_id', $bankProfileId);

            return $this->redirectToRoute('user_registration.register_verify_get');
        });
    }

    public function register_verify_get(Request $request, AuthyService $authyService)
    {
        $bankProfileId = $this->getSession()->get('bank_profile_id');
        $isSmsForced = $request->input('force_sms', false);

        if (!$authyService->requestSms($bankProfileId, $isSmsForced, $reason)) {
            $this->flashAlertMessage('error', $reason);
        }

        return $this->view(self::REGISTER_VERIFY_VIEW);
    }

    public function register_verify_post(UserRegisterVerifyFormRequest $request, AuthyService $authyService)
    {
        return $this->getGlobalLockoutViewOrContinue(self::REGISTER_VERIFY_VIEW, function () use ($request, $authyService) {
            $bankProfileId = $this->getSession()->get('bank_profile_id');
            $formInputs = $request->validated();

            if (!$authyService->verifyToken($bankProfileId, $formInputs['two_factor_token'])) {
                // User failed to verify a valid token.
                $this->incrementGlobalLockoutFailedCount(self::REGISTER_VERIFY_VIEW);
                $this->flashAlertMessage('error', $this->__('registration.user_verify_failed'));
                return $this->view(self::REGISTER_VERIFY_VIEW);
            }

            // User has successfully verified and should now bring you to register page.
            $this->resetGlobalLockoutFailedCount(self::REGISTER_VERIFY_VIEW);
            $this->getSession()->put('register_user_verified', true);
            return $this->redirectToRoute('user_registration.register_create_get');
        });
    }

    public function register_create_get()
    {
        return $this->view(self::REGISTER_CREATE_VIEW);
    }

    public function register_create_post(UserRegisterCreateFormRequest $request, UserAccount $userAccount)
    {
        $bankProfileId = $this->getSession()->get('bank_profile_id');
        $formInputs = $request->validated();

        $userAccount->createAccount($formInputs['username'], $formInputs['password'], $bankProfileId);

        $this->getSession()->forget('bank_profile_id');
        $this->flashAlertMessage('success', 'User has been successfully created!');
        return $this->redirectToRoute('user_authentication.login_get');
    }
}
