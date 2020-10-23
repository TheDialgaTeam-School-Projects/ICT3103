<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRegisterFormRequest;
use App\Http\Requests\UserTwoFactorRegisterFormRequest;
use App\Repository\UserRepositoryInterface;
use Authy\AuthyApi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserRegistrationController extends Controller
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

    public function index()
    {
        return view('user_register');
    }

    public function register(UserRegisterFormRequest $request)
    {
        $validated = $request->validated();
        $this->userRepository->createUser(
            $validated['username'],
            Hash::make($validated['password']),
            $validated['first_name'],
            $validated['last_name'],
            $validated['date_of_birth']
        );

        $request->session()->flash('alertType', 'success');
        $request->session()->flash('alertMessage', 'User has been successfully created!');

        return redirect()->route('user_authentication.index');
    }

    public function register_2fa(Request $request)
    {
        $session = $request->session();
        $data = [];

        if ($session->exists('alertType')) {
            $data['alertType'] = $session->get('alertType');
        }

        if ($session->exists('alertMessage')) {
            $data['alertMessage'] = $session->get('alertMessage');
        }

        return view('user_register_2fa', $data);
    }

    public function register_2fa_verify(UserTwoFactorRegisterFormRequest $request)
    {
        $validated = $request->validated();
        $authyUser = $this->authyApi->registerUser($validated['email_address'], $validated['mobile_number'], 65);

        if (!$authyUser->ok()) {
            return view('user_register_2fa', [
                'alertType' => 'error',
                'alertMessage' => 'Email or Mobile number is invalid.',
            ]);
        } else {
            $this->userRepository->registerOtpToUser($authyUser->id());
            return redirect()->route('user_authentication.login_2fa');
        }
    }
}
