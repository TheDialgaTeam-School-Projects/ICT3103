<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Http\Requests\UserLoginFormRequest;
use App\Http\Requests\UserTwoFactorLoginFormRequest;
use App\Models\UserAccount;
use App\Models\UserSession;
use App\Repository\UserRepositoryInterface;
use Authy\AuthyApi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

class UserAuthenticationController extends Controller
{
    private const USER_LOGIN_VIEW = 'user_login';

    public function login_get()
    {
        return Helper::viewWithAlertMessage(self::USER_LOGIN_VIEW);
    }

    public function login_post(UserLoginFormRequest $request, UserAccount $userAccount, UserSession $userSession)
    {
        return Helper::getLockoutOrContinue(self::USER_LOGIN_VIEW, function () use ($request, $userAccount, $userSession){
            $formInputs = $request->validated();

            if ($userAccount->isServingTimeout($formInputs['username'], $duration)) {
                // User is currently serving a timeout.
                Helper::flashAlertMessage('error', Helper::__('auth.throttle', ['seconds' => $duration]));
                return Helper::viewWithAlertMessage(self::USER_LOGIN_VIEW);
            }

            if (!Auth::attempt($request->only('username', 'password'))) {
                // User failed to log in.
                $userAccount->incrementFailedCount($formInputs['username']);
                Helper::incrementGlobalFailedCount(self::USER_LOGIN_VIEW);
                Helper::flashAlertMessage('error', Helper::__('auth.failed'));
                return Helper::viewWithAlertMessage(self::USER_LOGIN_VIEW);
            }

            // User has logged in.
            Helper::resetGlobalFailedCount(self::USER_LOGIN_VIEW);
            $userAccount->resetFailedCount($formInputs['username']);
            $userSession->logUserSession($formInputs['username'], $request->ip());
            return Helper::getRedirect()->route('user_authentication.login_2fa_get');
        });
    }

    public function login_2fa_get(Request $request, UserRepositoryInterface $userRepository, AuthyApi $authyApi)
    {
        if (App::isProduction()) {
            // For production, we need to send sms in order for user to authenticate.
            $authyApi->requestSms($userRepository->getAuthyId());
            return view('user_login_2fa', [
                'isOtpVerified' => $userRepository->isOtpVerified(),
            ]);
        } else {
            return $this->sendUserToDashboard($request);
        }
    }

    public function login_2fa_post(UserTwoFactorLoginFormRequest $request, UserRepositoryInterface $userRepository, AuthyApi $authyApi)
    {
        if ($userRepository->isOtpServingTimeout($duration)) {
            // User is currently serving a timeout.
            $this->flashAlertMessage($request, 'error', __('auth.throttle', ['seconds' => $duration]));
            return view('user_login_2fa', $this->includeAlertMessage($request));
        }

        $validated = $request->validated();
        $authyResponse = $authyApi->verifyToken($userRepository->getAuthyId(), $validated['2fa_token']);

        if ($authyResponse->ok()) {
            $userRepository->resetOtpFailedCount();

            if (!$userRepository->isOtpVerified()) {
                $userRepository->setOtpVerified();
            }

            return $this->sendUserToDashboard($request);
        } else {
            $userRepository->incrementOtpFailedCount();
            $this->flashAlertMessage($request, 'error', __('auth.otp_failed'));
            return view('user_login_2fa', $this->includeAlertMessage($request));
        }
    }

    public function login_check(Request $request, UserRepositoryInterface $userRepository)
    {
        $session = $request->session();
        $isLoggedIn = $session->get('LOGIN_VERIFIED', false);

        if ($isLoggedIn) {
            return $this->sendUserToDashboard($request, true);
        }

        return $this->sendUserToLogin($request, $userRepository);
    }

    public function logout()
    {
        Auth::logout();
        Helper::flashAlertMessage('success', Helper::__('auth.logged_out'));
        return Helper::getRedirect()->route('user_authentication.login_get');
    }

    private function sendUserToDashboard(Request $request, bool $skipSessionUpdate = false)
    {
        if (!$skipSessionUpdate) {
            $session = $request->session();
            $session->put('LOGIN_VERIFIED', true);
        }

        return redirect()->route('dashboard.index');
    }
}
