<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserLoginFormRequest;
use App\Http\Requests\UserTwoFactorLoginFormRequest;
use App\Models\UserAccount;
use App\Models\UserSession;
use App\Services\AuthyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

class UserAuthenticationController extends Controller
{
    public const LOGIN_VERIFIED_SESSION_TOKEN = 'login_verified';

    private const USER_LOGIN_VIEW = 'user_login';
    private const USER_2FA_VIEW = 'user_login_2fa';

    public function login_get()
    {
        return $this->view(self::USER_LOGIN_VIEW);
    }

    public function login_post(UserLoginFormRequest $request, UserAccount $userAccount, UserSession $userSession)
    {
        return $this->getGlobalLockoutViewOrContinue(self::USER_LOGIN_VIEW, function () use ($request, $userAccount, $userSession) {
            $formInputs = $request->validated();

            if ($userAccount->isServingTimeout($formInputs['username'], $duration)) {
                // User is currently serving a timeout.
                $this->flashAlertMessage('error', $this->__('auth.throttle', ['seconds' => $duration]));
                return $this->view(self::USER_LOGIN_VIEW);
            }

            if (!Auth::attempt($request->only('username', 'password'))) {
                // User failed to log in.
                $this->incrementGlobalLockoutFailedCount(self::USER_LOGIN_VIEW);
                $userAccount->incrementFailedCount($formInputs['username']);
                $this->flashAlertMessage('error', $this->__('auth.failed'));
                return $this->view(self::USER_LOGIN_VIEW);
            }

            // User has logged in.
            $this->resetGlobalLockoutFailedCount(self::USER_LOGIN_VIEW);
            $userAccount->resetFailedCount($formInputs['username']);
            $userSession->logUserSession($formInputs['username'], $request->ip());
            return $this->route('user_authentication.login_2fa_get');
        });
    }

    public function login_2fa_get(Request $request, AuthyService $authyService, UserAccount $userAccount)
    {
        if (App::isProduction()) {
            // For production, we need to send sms in order for user to authenticate.
            $bankProfileId = $userAccount->getBankProfileId(Auth::id());
            $isSmsForced = $request->input('force_sms', false);

            if (!$authyService->requestSms($bankProfileId, $isSmsForced, $reason)) {
                $this->flashAlertMessage('error', $reason);
            }

            return $this->view(self::USER_2FA_VIEW);
        } else {
            // For development, we want to save API cost hence this will skip the 2FA authentication.
            return $this->sendUserToDashboard();
        }
    }

    public function login_2fa_post(UserTwoFactorLoginFormRequest $request, AuthyService $authyService, UserAccount $userAccount)
    {
        return $this->getGlobalLockoutViewOrContinue(self::USER_2FA_VIEW, function () use ($request, $authyService, $userAccount) {
            if ($userAccount->isServingTimeout(Auth::id(), $duration)) {
                // User is currently serving a timeout.
                $this->flashAlertMessage('error', $this->__('auth.throttle', ['seconds' => $duration]));
                return view(self::USER_2FA_VIEW);
            }

            $bankProfileId = $userAccount->getBankProfileId(Auth::id());
            $formInputs = $request->validated();

            if (!$authyService->verifyToken($bankProfileId, $formInputs['two_factor_token'])) {
                // User failed to verify a valid token.
                $this->incrementGlobalLockoutFailedCount(self::USER_2FA_VIEW);
                $userAccount->incrementFailedCount(Auth::id());
                $this->flashAlertMessage('error', $this->__('auth.otp_failed'));
                return $this->view(self::USER_2FA_VIEW);
            }

            // User has successfully verified and should now bring you to register page.
            $this->resetGlobalLockoutFailedCount(self::USER_2FA_VIEW);
            $userAccount->resetFailedCount(Auth::id());
            return $this->sendUserToDashboard();
        });
    }

    public function logout()
    {
        Auth::logout();
        $this->getSession()->forget(self::LOGIN_VERIFIED_SESSION_TOKEN);
        $this->flashAlertMessage('success', $this->__('auth.logged_out'));
        return $this->route('user_authentication.login_get');
    }

    public function login_check()
    {
        $isTwoFactorVerified = $this->getSession()->get(self::LOGIN_VERIFIED_SESSION_TOKEN, false);

        if ($isTwoFactorVerified) {
            return $this->sendUserToDashboard(true);
        } else {
            return $this->route('user_authentication.login_2fa_get');
        }
    }

    private function sendUserToDashboard(bool $skipSessionUpdate = false)
    {
        if (!$skipSessionUpdate) {
            $this->getSession()->put(self::LOGIN_VERIFIED_SESSION_TOKEN, true);
        }

        return $this->route('dashboard.bank_account_list');
    }
}
