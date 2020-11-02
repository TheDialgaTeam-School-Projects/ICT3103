<?php
/** @noinspection PhpUnhandledExceptionInspection */

/** @noinspection PhpDocMissingThrowsInspection */

namespace App\Helpers\Traits;

use Illuminate\Container\Container;
use Illuminate\Session\SessionManager;
use Illuminate\Session\Store;

trait SessionHelperTrait
{
    use ConfigHelperTrait, TranslatorHelperTrait;

    /**
     * @var Store|SessionManager
     */
    private static $session;

    /**
     * Flash alert message to the session.
     *
     * @param string $alertType Alert type.
     * @param string $alertMessage Alert message.
     */
    public static function flashAlertMessage(string $alertType, string $alertMessage): void
    {
        self::getSession()->flash('alertType', $alertType);
        self::getSession()->flash('alertMessage', $alertMessage);
    }

    public static function checkSessionBeforeContinue(string $redirectRoute, array $sessionKeys, callable $continuation)
    {
        if (!array_reduce(array_map(function ($key) {
            return self::getSession()->has($key);
        }, $sessionKeys), function ($a, $b) {
            return $a && $b;
        }, true)) {
            // Session is probably expired or invalid.
            self::flashAlertMessage('error', self::__('common.invalid_session'));
            return self::getRedirect()->route($redirectRoute);
        }

        $sessionData = [];

        foreach ($sessionKeys as $sessionKey) {
            $sessionData[$sessionKey] = self::getSession()->get($sessionKey);
        }

        return $continuation($sessionData);
    }

    /**
     * @return SessionManager|Store
     */
    public static function getSession()
    {
        if (!isset(self::$session)) {
            self::$session = Container::getInstance()->make('session');
        }

        return self::$session;
    }
}
