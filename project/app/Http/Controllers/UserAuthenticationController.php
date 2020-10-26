<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserLoginFormRequest;
use App\Http\Requests\UserTwoFactorLoginFormRequest;
use App\Repository\UserRepositoryInterface;
use Authy\AuthyApi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserAuthenticationController extends Controller
{
    public function login_get(Request $request)
    {
        $data = [];
        $this->includeAlertMessage($request, $data);
        return view('user_login', $data);
    }

    public function login_post(UserLoginFormRequest $request, UserRepositoryInterface $userRepository)
    {
        if ($this->isServingGlobalTimeout($request, 'user_login')) {
            // User is currently serving a global timeout for brute forcing.
            return view('user_login', [
                'alertType' => 'error',
                'alertMessage' => 'Account authentication has been disabled due to mass failed attempt. Please try again later.',
            ]);
        }

        $user = $userRepository->findUserAccount($request->get('username'));

        if ($userRepository->isUserServingTimeout($user)) {
            // User is currently serving a timeout.
            return view('user_login', [
                'alertType' => 'error',
                'alertMessage' => 'Account has been locked due to mass failed attempt. Please try again later.',
            ]);
        }

        if (Auth::attempt($request->only('username', 'password'))) {
            // User has logged in.
            $this->resetGlobalFailedCount($request, 'user_login');
            $userRepository->resetUserFailedCount($user);
            $userRepository->logUserSession($request->ip(), $user);

            return $this->sendUserToLogin($request, $userRepository);
        }

        // User failed to log in.
        if ($user === null) {
            $this->incrementGlobalFailedCount($request, 'user_login');
        } else {
            $userRepository->incrementUserFailedCount($user);
        }

        return view('user_login', [
            'alertType' => 'error',
            'alertMessage' => 'Either username or password is invalid.',
        ]);
    }

    public function login_2fa_get(Request $request, UserRepositoryInterface $userRepository, AuthyApi $authyApi)
    {
        if (env('APP_ENV') !== 'local') {
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
        if ($userRepository->isOtpServingTimeout()) {
            // User is currently serving a timeout.
            return view('user_login_2fa', [
                'alertType' => 'error',
                'alertMessage' => 'Two-factor authentication has been locked due to mass failed attempt. Please try again later.',
            ]);
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

            return view('user_login_2fa', [
                'alertType' => 'error',
                'alertMessage' => 'Invalid token.',
            ]);
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

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->flash('alertType', 'success');
        $request->session()->flash('alertMessage', 'User has been successfully logged out!');

        return redirect()->route('user_authentication.login_get');
    }

    private function sendUserToLogin(Request $request, UserRepositoryInterface $userRepository)
    {
        if ($userRepository->isOtpRegistered()) {
            // User has registered the OTP token, ask them to verify now.
            return redirect()->route('user_authentication.login_2fa_get');
        } else {
            // User has not register the OTP token, ask them to register now.
            $this->flashAlertMessage($request, 'warning', 'You are required to setup Two-factor authentication before you are allowed access to your account.');
            return redirect()->route('user_registration.register_2fa_get');
        }
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
