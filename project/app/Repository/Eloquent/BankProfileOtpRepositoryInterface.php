<?php

namespace App\Repository\Eloquent;

use App\Models\BankProfile;

interface BankProfileOtpRepositoryInterface
{
    /**
     * Register bank profile otp.
     *
     * @param int $authyId Authy id.
     * @param BankProfile|null $bankProfile [optional] Target bank profile. null for current logged in user bank profile.
     * @return bool true if insert query is successful, else false.
     */
    public function registerBankProfileOtp(int $authyId, BankProfile $bankProfile = null): bool;

    /**
     * Register a list of bank profile otp.
     *
     * @param array $bankProfileOtpCollection List of bank profile otp.
     * @return bool true if insert query is successful, else false.
     */
    public function registerBankProfileOtpInBulk(array $bankProfileOtpCollection): bool;

    /**
     * Get authy id registered to the bank profile.
     *
     * @param BankProfile|null $bankProfile [optional] Target bank profile. null for current logged in user bank profile.
     * @return int authy id.
     */
    public function getAuthyId(BankProfile $bankProfile = null): int;

    /**
     * Get if the otp is registered.
     *
     * @param BankProfile|null $bankProfile [optional] Target bank profile. null for current logged in user bank profile.
     * @return bool true if otp is registered, else false.
     */
    public function isOtpRegistered(BankProfile $bankProfile = null): bool;

    /**
     * Check if the otp is verified.
     *
     * @param BankProfile|null $bankProfile [optional] Target bank profile. null for current logged in user bank profile.
     * @return bool true if otp is verified, else false.
     */
    public function isOtpVerified(BankProfile $bankProfile = null): bool;

    /**
     * Check if the otp is currently serving a timeout.
     *
     * @param int|null $duration [optional] Return the remaining timeout duration.
     * @param BankProfile|null $bankProfile [optional] Target bank profile. null for current logged in user bank profile.
     * @return bool true if otp is current serving a timeout, else false.
     */
    public function isOtpServingTimeout(int &$duration = null, BankProfile $bankProfile = null): bool;

    /**
     * Increment otp failed count by 1.
     *
     * @param BankProfile|null $bankProfile [optional] Target bank profile. null for current logged in user bank profile.
     * @return bool true if update query is successful, else false.
     */
    public function incrementOtpFailedCount(BankProfile $bankProfile = null): bool;

    /**
     * Reset otp failed count to 0.
     *
     * @param BankProfile|null $bankProfile [optional] Target bank profile. null for current logged in user bank profile.
     * @return bool true if update query is successful, else false.
     */
    public function resetOtpFailedCount(BankProfile $bankProfile = null): bool;

    /**
     * Set otp has verified status.
     *
     * @param BankProfile|null $bankProfile [optional] Target bank profile. null for current logged in user bank profile.
     * @return bool true if update query is successful, else false.
     */
    public function setOtpVerified(BankProfile $bankProfile = null): bool;

    /**
     * Update otp token of the selected profile.
     *
     * @param int $authyId Authy id.
     * @param BankProfile|null $bankProfile [optional] Target bank profile. null for current logged in user bank profile.
     * @return bool true if update query is successful, else false.
     */
    public function updateBankProfileOtp(int $authyId, BankProfile $bankProfile = null): bool;
}
