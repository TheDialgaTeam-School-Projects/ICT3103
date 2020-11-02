<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
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
        return Helper::viewWithAlertMessage(self::REGISTER_IDENTIFY_VIEW);
    }

    public function register_identify_post(UserRegisterIdentifyFormRequest $request, BankProfile $bankProfile)
    {
        return Helper::getLockoutOrContinue(self::REGISTER_IDENTIFY_VIEW, function () use ($request, $bankProfile) {
            $formInputs = $request->validated();

            if (!$bankProfile->isValidBankProfile($formInputs['identification_id'], $formInputs['date_of_birth'])) {
                // User seem to have failed the identification check on the bank profile, this is probably because of typo or trying to brute force their way through.
                Helper::incrementGlobalFailedCount(self::REGISTER_IDENTIFY_VIEW);
                Helper::flashAlertMessage('error', Helper::__('registration.user_identify_failed'));
                return Helper::viewWithAlertMessage(self::REGISTER_IDENTIFY_VIEW);
            }

            // User is identified.
            $bankProfileId = $bankProfile->getBankProfileId($formInputs['identification_id']);

            if ($bankProfile->isUserAccountCreated($bankProfileId)) {
                // User seem to have owned an account.
                Helper::incrementGlobalFailedCount(self::REGISTER_IDENTIFY_VIEW);
                Helper::flashAlertMessage('error', Helper::__('registration.user_identify_user_has_account'));
                return Helper::viewWithAlertMessage(self::REGISTER_IDENTIFY_VIEW);
            }

            // User does not own an account, proceed to verification.
            Helper::resetGlobalFailedCount(self::REGISTER_IDENTIFY_VIEW);
            Helper::getSession()->put('bank_profile_id', $bankProfileId);
            return Helper::getRedirect()->route('user_registration.register_verify_get');
        });
    }

    public function register_verify_get(Request $request, AuthyService $authyService)
    {
        return Helper::checkSessionBeforeContinue('user_registration.register_identify_get', ['bank_profile_id'], function ($sessionData) use ($request, $authyService) {
            $bankProfileId = $sessionData['bank_profile_id'];
            $isSmsForced = $request->input('force_sms', false);

            if (!$authyService->requestSms($bankProfileId, $isSmsForced, $reason)) {
                Helper::flashAlertMessage('error', $reason);
            }

            return Helper::viewWithAlertMessage(self::REGISTER_VERIFY_VIEW);
        });
    }

    public function register_verify_post(UserRegisterVerifyFormRequest $request, AuthyService $authyService)
    {
        return Helper::checkSessionBeforeContinue('user_registration.register_identify_get', ['bank_profile_id'], function ($sessionData) use ($request, $authyService) {
            return Helper::getLockoutOrContinue(self::REGISTER_VERIFY_VIEW, function () use ($sessionData, $request, $authyService) {
                $bankProfileId = $sessionData['bank_profile_id'];
                $formInputs = $request->validated();

                if (!$authyService->verifyToken($bankProfileId, $formInputs['two_factor_token'])) {
                    // User failed to verify a valid token.
                    Helper::incrementGlobalFailedCount(self::REGISTER_VERIFY_VIEW);
                    Helper::flashAlertMessage('error', Helper::__('registration.user_verify_failed'));
                    return Helper::viewWithAlertMessage(self::REGISTER_VERIFY_VIEW);
                }

                // User has successfully verified and should now bring you to register page.
                Helper::resetGlobalFailedCount(self::REGISTER_VERIFY_VIEW);
                return Helper::getRedirect()->route('user_registration.register_create_get');
            });
        });
    }

    public function register_create_get()
    {
        return Helper::checkSessionBeforeContinue('user_registration.register_identify_get', ['bank_profile_id'], function () {
            return Helper::viewWithAlertMessage(self::REGISTER_CREATE_VIEW);
        });
    }

    public function register_create_post(UserRegisterCreateFormRequest $request, UserAccount $userAccount)
    {
        return Helper::checkSessionBeforeContinue('user_registration.register_identify_get', ['bank_profile_id'], function ($sessionData) use ($request, $userAccount) {
            $formInputs = $request->validated();
            $userAccount->createAccount($formInputs['username'], $formInputs['password'], $sessionData['bank_profile_id']);
            Helper::flashAlertMessage('success', 'User has been successfully created!');
            return Helper::getRedirect()->route('user_authentication.login_get');
        });
    }
}
