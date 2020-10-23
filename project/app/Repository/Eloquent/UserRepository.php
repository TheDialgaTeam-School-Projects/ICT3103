<?php

namespace App\Repository\Eloquent;

use App\Models\User;
use App\Models\UserOtp;
use App\Models\UserSession;
use App\Repository\UserRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserRepository implements UserRepositoryInterface
{
    private const USER_LOCKOUT_TRIES = 3;
    private const USER_LOCKOUT_DURATION = 5;

    private const USER_OTP_LOCKOUT_TRIES = 3;
    private const USER_OTP_LOCKOUT_DURATION = 5;

    /**
     * @var User
     */
    private $userModel;

    /**
     * @var UserSession
     */
    private $userSessionModel;

    /**
     * @var UserOtp
     */
    private $userOtpModel;

    public function __construct(User $userModel, UserSession $userSessionModel, UserOtp $userOtpModel)
    {
        $this->userModel = $userModel;
        $this->userSessionModel = $userSessionModel;
        $this->userOtpModel = $userOtpModel;
    }

    public function createUser(string $username, string $password, string $firstName, string $lastName, $dateOfBirth): User
    {
        return $this->userModel->create([
            'username' => $username,
            'password' => Hash::make($password),
            'first_name' => $firstName,
            'last_name' => $lastName,
            'date_of_birth' => $dateOfBirth
        ]);
    }

    public function createBulkUsers(array $users): void
    {
        foreach ($users as $user) {
            $createdUser = $this->userModel->create([
                'username' => $user['username'],
                'password' => array_key_exists('password', $user) ? Hash::make($user['password']) : $user['hashed_password'],
                'first_name' => $user['first_name'],
                'last_name' => $user['last_name'],
                'date_of_birth' => $user['date_of_birth'],
            ]);

            if (array_key_exists('authy_id', $user)) {
                $this->registerOtpToUser($user['authy_id'], $createdUser);
            }
        }
    }

    public function findUserById(int $id): ?User
    {
        return $this->userModel->find($id);
    }

    public function findUserByUsername(string $username): ?User
    {
        return $this->userModel->firstWhere('username', $username);
    }

    public function isUserInTimeout(User $user = null): bool
    {
        $user = $this->tryGetLoggedInUser($user);
        return isset($user, $user->reset_datetime) && Carbon::now()->lessThan($user->reset_datetime);
    }

    public function incrementUserFailedCount(User $user = null): bool
    {
        $user = $this->tryGetLoggedInUser($user);
        if (!isset($user)) return false;

        $user->failed_count++;

        if ($user->failed_count >= self::USER_LOCKOUT_TRIES) {
            $user->failed_count = 0;
            $user->reset_datetime = Carbon::now()->addMinutes(self::USER_LOCKOUT_DURATION);
        }

        return $user->save();
    }

    public function resetUserFailedCount(User $user = null): bool
    {
        $user = $this->tryGetLoggedInUser($user);
        if (!isset($user)) return false;

        $user->failed_count = 0;
        return $user->save();
    }

    public function logUserSession(string $ipAddress, User $user = null): bool
    {
        $user = $this->tryGetLoggedInUser($user);
        if (!isset($user)) return false;

        $this->userSessionModel->create([
            'user_account_id' => $user->id,
            'ip_address' => $ipAddress,
            'last_logged_in' => Carbon::now()
        ]);

        return true;
    }

    public function registerOtpToUser(int $authyId, User $user = null): bool
    {
        $user = $this->tryGetLoggedInUser($user);
        if (!isset($user)) return false;

        $this->userOtpModel->create([
            'user_account_id' => $user->id,
            'authy_id' => $authyId,
        ]);

        return true;
    }

    public function isOtpRegistered(User $user = null): bool
    {
        $user = $this->tryGetLoggedInUser($user);
        return isset($user, $user->otp);
    }

    public function isOtpInTimeout(User $user = null): bool
    {
        $user = $this->tryGetLoggedInUser($user);
        return isset($user, $user->otp, $user->otp->reset_datetime) && Carbon::now()->lessThan($user->otp->reset_datetime);
    }

    public function getAuthyId(User $user = null): ?int
    {
        $user = $this->tryGetLoggedInUser($user);
        if (!isset($user, $user->otp)) return null;
        return $user->otp->authy_id;
    }

    public function incrementOtpFailedCount(User $user = null): bool
    {
        $user = $this->tryGetLoggedInUser($user);
        if (!isset($user, $user->otp)) return false;

        $user->otp->failed_count++;

        if ($user->otp->failed_count >= self::USER_OTP_LOCKOUT_TRIES) {
            $user->otp->failed_count = 0;
            $user->otp->reset_datetime = Carbon::now()->addMinutes(self::USER_OTP_LOCKOUT_DURATION);
        }

        return $user->save();
    }

    public function resetOtpFailedCount(User $user = null): bool
    {
        $user = $this->tryGetLoggedInUser($user);
        if (!isset($user, $user->otp)) return false;

        $user->otp->failed_count = 0;
        return $user->save();
    }

    private function tryGetLoggedInUser(?User $user): ?User
    {
        if (isset($user)) return $user;

        // Attempt to get logged in user id.
        $userId = Auth::id();
        if (!isset($userId)) return null;

        return $this->findUserById($userId);
    }
}
