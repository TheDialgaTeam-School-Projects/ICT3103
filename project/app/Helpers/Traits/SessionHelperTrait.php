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
