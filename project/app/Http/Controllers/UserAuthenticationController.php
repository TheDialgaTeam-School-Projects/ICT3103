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
    /**
     * @var UserRepositoryInterface
     */
    private $userRepository;

    /**
     * @var AuthyApi
     */
    private $authyApi;

    public function __construct(UserRepositoryInterface $userRepository, AuthyApi $authyApi)
    {
        $this->userRepository = $userRepository;
        $this->authyApi = $authyApi;
    }

    public function index(Request $request)
    {
        $session = $request->session();
        $data = [];

        if ($session->exists('alertType')) {
            $data['alertType'] = $session->get('alertType');
        }

        if ($session->exists('alertMessage')) {
            $data['alertMessage'] = $session->get('alertMessage');
        }

        return view('user_login', $data);
    }

    public function login(UserLoginFormRequest $request)
    {
        $user = $this->userRepository->findUserByUsername($request->get('username'));

        if ($this->userRepository->isUserInTimeout($user)) {
            // User is currently serving a timeout.
            return view('user_login', [
                'alertType' => 'error',
                'alertMessage' => 'Account has been locked due to mass attempt. Please try again later.',
            ]);
        }

        if (Auth::attempt($request->only('username', 'password'))) {
            // Valid user
            $this->userRepository->resetUserFailedCount($user);
            $this->userRepository->logUserSession($request->ip(), $user);

            if ($this->userRepository->isOtpRegistered($user)) {
                return redirect()->intended(route('user_authentication.login_2fa'));
            } else {
                $request->session()->flash('alertType', 'warning');
                $request->session()->flash('alertMessage', 'You are required to setup Two-factor authentication before you are allowed access to your account.');

                return redirect()->intended(route('user_registration.register_2fa'));
            }
        } else {
            // Invalid user or password.
            $this->userRepository->incrementUserFailedCount($user);

            return view('user_login', [
                'alertType' => 'error',
                'alertMessage' => 'Either username or password is invalid.',
            ]);
        }
    }

    public function login_2fa()
    {
        if (env('APP_ENV') !== 'local') {
            // For production, we need to send sms in order for user to authenticate.
            $this->authyApi->requestSms($this->userRepository->getAuthyId());
        }

        return view('user_login_2fa');
    }

    public function login_2fa_verify(UserTwoFactorLoginFormRequest $request)
    {
        if ($this->userRepository->isOtpInTimeout()) {
            // User is currently serving a timeout.
            return view('user_login_2fa', [
                'alertType' => 'error',
                'alertMessage' => 'Two-factor authentication has been locked due to mass attempt. Please try again later.',
            ]);
        }

        $session = $request->session();

        if (env('APP_ENV') === 'local') {
            // For local development, we disable 2fa so that we do not hit the free api call limit.
            $this->userRepository->resetOtpFailedCount();
            $session->put('LOGIN_VERIFIED', true);
            return redirect()->route('dashboard.index');
        }

        $validated = $request->validated();
        $authyResponse = $this->authyApi->verifyToken($this->userRepository->getAuthyId(), $validated['2fa_token']);

        if ($authyResponse->ok()) {
            $this->userRepository->resetOtpFailedCount();
            $session->put('LOGIN_VERIFIED', true);
            return redirect()->route('dashboard.index');
        } else {
            $this->userRepository->incrementOtpFailedCount();

            return view('user_login_2fa', [
                'alertType' => 'error',
                'alertMessage' => 'Invalid token.',
            ]);
        }
    }

    public function login_check(Request $request)
    {
        $session = $request->session();
        $isLoggedIn = $session->get('LOGIN_VERIFIED', false);

        if ($isLoggedIn) {
            return redirect()->route('dashboard.index');
        }

        if ($this->userRepository->isOtpRegistered()) {
            return redirect()->route('user_authentication.login_2fa');
        } else {
            $request->session()->flash('alertType', 'warning');
            $request->session()->flash('alertMessage', 'You are required to setup Two-factor authentication before you are allowed access to your account.');
            return redirect()->route('user_registration.register_2fa');
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->flash('alertType', 'success');
        $request->session()->flash('alertMessage', 'User has been successfully logged out!');

        return redirect()->route('user_authentication.index');
    }
}
