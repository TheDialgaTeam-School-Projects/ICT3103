<?php
/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection PhpDocMissingThrowsInspection */

namespace App\Helpers\Traits;

use Illuminate\Container\Container;
use Illuminate\Session\SessionManager;
use Illuminate\Session\Store;

trait SessionHelperTrait
{
    /**
     * @var Store|SessionManager
     */
    private static $session;

    /**
     * Get / set the specified session value.
     *
     * If an array is passed as the key, we will assume you want to set an array of values.
     *
     * @param  array|string|null  $key
     * @param  mixed  $default
     * @return mixed|Store|SessionManager
     */
    public static function session($key = null, $default = null)
    {
        if (is_null($key)) {
            return self::getSession();
        }

        if (is_array($key)) {
            self::getSession()->put($key);
        }

        return self::getSession()->get($key, $default);
    }

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
