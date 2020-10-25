<?php

namespace App\Repository;

use App\Models\BankAccount;
use App\Models\BankProfile;
use App\Models\BankProfileOtp;
use App\Models\BankTransaction;
use App\Models\UserAccount;
use App\Models\UserSession;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserRepository implements UserRepositoryInterface
{
    /**
     * Amount of tries before user account is lockout.
     */
    private const USER_ACCOUNT_LOCKOUT_TRIES = 3;

    /**
     * Duration in minutes of serving the lockout.
     */
    private const USER_ACCOUNT_LOCKOUT_DURATION = 5;

    /**
     * Amount of tries before otp is lockout.
     */
    private const BANK_PROFILE_OTP_LOCKOUT_TRIES = 3;

    /**
     * Duration in minutes of serving the lockout.
     */
    private const BANK_PROFILE_OTP_LOCKOUT_DURATION = 5;

    /**
     * @var BankProfile
     */
    private $bankProfile;

    /**
     * @var BankProfileOtp
     */
    private $bankProfileOtp;

    /**
     * @var BankAccount
     */
    private $bankAccount;

    /**
     * @var BankTransaction
     */
    private $bankTransaction;

    /**
     * @var UserAccount
     */
    private $userAccount;

    /**
     * @var UserSession
     */
    private $userSession;

    public function __construct(BankProfile $bankProfile,
                                BankProfileOtp $bankProfileOtp,
                                BankAccount $bankAccount,
                                BankTransaction $bankTransaction,
                                UserAccount $userAccount,
                                UserSession $userSession)
    {
        $this->bankProfile = $bankProfile;
        $this->bankProfileOtp = $bankProfileOtp;
        $this->bankAccount = $bankAccount;
        $this->bankTransaction = $bankTransaction;
        $this->userAccount = $userAccount;
        $this->userSession = $userSession;
    }

    /**
     * @inheritDoc
     */
    public function createBankProfileInBulk(array $bankProfiles): bool
    {
        return $this->bankProfile->insert($bankProfiles);
    }

    /**
     * @inheritDoc
     */
    public function isBankProfileValid(string $id, string $identificationId, string $dateOfBirth): bool
    {
        /** @var BankProfile $bankProfile */
        $bankProfile = $this->bankProfile->find($id);
        return $bankProfile->identification_id === $identificationId && $bankProfile->date_of_birth->isSameAs('Y-m-d', $dateOfBirth);
    }

    /**
     * @inheritDoc
     */
    public function registerBankProfileOtp(int $authyId, BankProfile $bankProfile = null): bool
    {
        $bankProfile = $this->tryGetLoggedInBankProfile($bankProfile);
        if (!isset($bankProfile)) return false;

        return $this->bankProfileOtp->insert([
            'authy_id' => $authyId,
            'bank_profile_id' => $bankProfile->id,
        ]);
    }

    /**
     * @inheritDoc
     */
    public function registerBankProfileOtpInBulk(array $bankProfileOtpCollection): bool
    {
        return $this->bankProfileOtp->insert($bankProfileOtpCollection);
    }

    /**
     * @inheritDoc
     */
    public function getAuthyId(BankProfile $bankProfile = null): int
    {
        $bankProfile = $this->tryGetLoggedInBankProfile($bankProfile);
        if (!isset($bankProfile, $bankProfile->otp)) return false;
        return $bankProfile->otp->authy_id;
    }

    /**
     * @inheritDoc
     */
    public function isOtpRegistered(BankProfile $bankProfile = null): bool
    {
        $bankProfile = $this->tryGetLoggedInBankProfile($bankProfile);
        return isset($bankProfile, $bankProfile->otp);
    }

    /**
     * @inheritdoc
     */
    public function isOtpVerified(BankProfile $bankProfile = null): bool
    {
        $bankProfile = $this->tryGetLoggedInBankProfile($bankProfile);
        return isset($bankProfile, $bankProfile->otp) && $bankProfile->otp->authy_is_verified;
    }

    /**
     * @inheritDoc
     */
    public function isOtpServingTimeout(BankProfile $bankProfile = null): bool
    {
        $bankProfile = $this->tryGetLoggedInBankProfile($bankProfile);
        return isset($bankProfile, $bankProfile->otp, $bankProfile->otp->authy_reset_datetime) && Carbon::now()->lessThan($bankProfile->otp->authy_reset_datetime);
    }

    /**
     * @inheritDoc
     */
    public function incrementOtpFailedCount(BankProfile $bankProfile = null): bool
    {
        $bankProfile = $this->tryGetLoggedInBankProfile($bankProfile);
        if (!isset($bankProfile, $bankProfile->otp)) return false;

        $bankProfile->otp->authy_failed_count++;

        if ($bankProfile->otp->authy_failed_count >= self::BANK_PROFILE_OTP_LOCKOUT_TRIES) {
            $bankProfile->otp->authy_failed_count = 0;
            $bankProfile->otp->authy_reset_datetime = Carbon::now()->addMinutes(self::BANK_PROFILE_OTP_LOCKOUT_DURATION);
        }

        return $bankProfile->push();
    }

    /**
     * @inheritDoc
     */
    public function resetOtpFailedCount(BankProfile $bankProfile = null): bool
    {
        $bankProfile = $this->tryGetLoggedInBankProfile($bankProfile);
        if (!isset($bankProfile, $bankProfile->otp)) return false;

        $bankProfile->otp->authy_failed_count = 0;
        return $bankProfile->push();
    }

    /**
     * @inheritdoc
     */
    public function setOtpVerified(BankProfile $bankProfile = null): bool
    {
        $bankProfile = $this->tryGetLoggedInBankProfile($bankProfile);
        if (!isset($bankProfile, $bankProfile->otp)) return false;

        $bankProfile->otp->authy_is_verified = true;
        return $bankProfile->push();
    }

    /**
     * @inheritdoc
     */
    public function updateBankProfileOtp(int $authyId, BankProfile $bankProfile = null): bool
    {
        $bankProfile = $this->tryGetLoggedInBankProfile($bankProfile);
        if (!isset($bankProfile, $bankProfile->otp)) return false;

        $bankProfile->otp->authy_id = $authyId;
        return $bankProfile->push();
    }

    /**
     * @inheritDoc
     */
    public function createBankAccountInBulk(array $bankAccounts): bool
    {
        return $this->bankAccount->insert($bankAccounts);
    }

    /**
     * @inheritDoc
     */
    public function createUserAccount(string $username, string $password, string $bankProfileId): bool
    {
        return $this->userAccount->insert([
            'username' => $username,
            'password' => Hash::make($password),
            'bank_profile_id' => $bankProfileId,
        ]);
    }

    /**
     * @inheritDoc
     */
    public function createUserAccountInBulk(array $userAccounts): bool
    {
        return $this->userAccount->insert($userAccounts);
    }

    /**
     * @inheritDoc
     */
    public function findUserAccount(string $username): ?UserAccount
    {
        return $this->userAccount->firstWhere('username', $username);
    }

    /**
     * @inheritdoc
     */
    public function isUserAccountCreated(string $bankProfileId): bool
    {
        /** @var ?UserAccount $userAccount */
        $userAccount = $this->userAccount->firstWhere('bank_profile_id', $bankProfileId);
        return isset($userAccount);
    }

    /**
     * @inheritDoc
     */
    public function isUserServingTimeout(UserAccount $userAccount = null): bool
    {
        $userAccount = $this->tryGetLoggedInUserAccount($userAccount);
        return isset($userAccount, $userAccount->password_reset_datetime) && Carbon::now()->lessThan($userAccount->password_reset_datetime);
    }

    /**
     * @inheritDoc
     */
    public function incrementUserFailedCount(UserAccount $userAccount = null): bool
    {
        $userAccount = $this->tryGetLoggedInUserAccount($userAccount);
        if (!isset($userAccount)) return false;

        $userAccount->password_failed_count++;

        if ($userAccount->password_failed_count >= self::USER_ACCOUNT_LOCKOUT_TRIES) {
            $userAccount->password_failed_count = 0;
            $userAccount->password_reset_datetime = Carbon::now()->addMinutes(self::USER_ACCOUNT_LOCKOUT_DURATION);
        }

        return $userAccount->save();
    }

    /**
     * @inheritDoc
     */
    public function resetUserFailedCount(UserAccount $userAccount = null): bool
    {
        $userAccount = $this->tryGetLoggedInUserAccount($userAccount);
        if (!isset($userAccount)) return false;

        $userAccount->password_failed_count = 0;
        return $userAccount->save();
    }

    /**
     * @inheritDoc
     */
    public function logUserSession(string $ipAddress, UserAccount $userAccount = null): bool
    {
        $userAccount = $this->tryGetLoggedInUserAccount($userAccount);
        if (!isset($userAccount)) return false;

        return $this->userSession->insert([
            'username' => $userAccount->username,
            'ip_address' => $ipAddress,
            'last_logged_in' => Carbon::now(),
        ]);
    }

    /**
     * Try to get logged in user account.
     *
     * @param UserAccount|null $userAccount [optional] Target user account. null for current logged in user.
     * @return UserAccount|null logged in user account or null if not found.
     */
    private function tryGetLoggedInUserAccount(?UserAccount $userAccount): ?UserAccount
    {
        if (isset($userAccount)) return $userAccount;

        // Attempt to get logged in user id.
        $userId = Auth::id();
        if (!isset($userId)) return null;

        return $this->findUserAccount($userId);
    }

    /**
     * Try to get logged in user account.
     *
     * @param BankProfile|null $bankProfile [optional] Target bank profile. null for current logged in user bank profile.
     * @return BankProfile|null logged in user account or null if not found.
     */
    private function tryGetLoggedInBankProfile(?BankProfile $bankProfile): ?BankProfile
    {
        if (isset($bankProfile)) return $bankProfile;

        $user = $this->tryGetLoggedInUserAccount(null);
        if (!isset($user)) return null;

        return $user->bankProfile;
    }
}
