<?php
/** @noinspection PhpUnhandledExceptionInspection */

/** @noinspection PhpDocMissingThrowsInspection */

namespace App\Helpers\Traits;

use Illuminate\Container\Container;
use Illuminate\Routing\Redirector;

trait RedirectHelperTrait
{
    /**
     * @var Redirector
     */
    private static $redirect;

    /**
     * @return Redirector
     */
    public static function getRedirect()
    {
        if (!isset(self::$redirect)) {
            self::$redirect = Container::getInstance()->make('redirect');
        }

        return self::$redirect;
    }
}
