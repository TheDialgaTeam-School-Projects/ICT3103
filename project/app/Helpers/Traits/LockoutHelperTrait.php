<?php
/** @noinspection PhpUnhandledExceptionInspection */

/** @noinspection PhpDocMissingThrowsInspection */

namespace App\Helpers\Traits;

use Carbon\Carbon;

trait LockoutHelperTrait
{
    use ConfigHelperTrait, SessionHelperTrait, TranslatorHelperTrait, ViewHelperTrait;

    public static function getLockoutOrContinue(string $lockoutView, callable $continuation)
    {
        if (self::isHavingGlobalTimeout($lockoutView, $duration)) {
            // User is currently serving a global timeout for trying to brute force the identification process.
            self::flashAlertMessage('error', self::__('common.lockout_message', ['seconds', $duration]));
            return self::viewWithAlertMessage($lockoutView);
        }

        return $continuation();
    }

    public static function incrementGlobalFailedCount(string $key): void
    {
        $failedCount = self::getSession()->get(self::getFailedCountKey($key), 0);
        $failedCount++;

        if ($failedCount >= self::getMaxAttempt()) {
            $failedCount = 0;
            self::getSession()->put(self::getResetTimestampKey($key), Carbon::now()->addMinutes(self::getLockoutDuration())->getTimestamp());
        }

        self::getSession()->put(self::getFailedCountKey($key), $failedCount);
    }

    public static function resetGlobalFailedCount(string $key): void
    {
        self::getSession()->put(self::getFailedCountKey($key), 0);
    }

    public static function isHavingGlobalTimeout(string $key, int &$duration = null): bool
    {
        /** @var int $resetTimestamp */
        $resetTimestamp = self::getSession()->get(self::getResetTimestampKey($key));
        if (!isset($resetTimestamp)) return false;

        $currentTimeStamp = Carbon::now()->getTimestamp();
        if ($currentTimeStamp >= $resetTimestamp) return false;

        $duration = $resetTimestamp - $currentTimeStamp;
        return true;
    }

    private static function getFailedCountKey(string $key): string
    {
        return $key . self::getConfig()->get('lockout.global.session.failed_count');
    }

    private static function getResetTimestampKey(string $key): string
    {
        return $key . self::getConfig()->get('lockout.global.session.reset_timestamp');
    }

    private static function getMaxAttempt(): int
    {
        return self::getConfig()->get('lockout.global.max_attempt');
    }

    private static function getLockoutDuration(): int
    {
        return self::getConfig()->get('lockout.global.lockout_duration');
    }
}
