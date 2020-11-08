<?php
/** @noinspection PhpDocMissingThrowsInspection */
/** @noinspection PhpUnhandledExceptionInspection */

namespace App\Helpers\Traits;

use Illuminate\Container\Container;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;

trait RedirectorHelperTraits
{
    /**
     * @var Redirector
     */
    private static $redirector;

    /**
     * Get an instance of the redirector.
     *
     * @param string|null $to
     * @param int $status
     * @param array $headers
     * @param bool|null $secure
     * @return Redirector|RedirectResponse
     */
    public static function redirect($to = null, $status = 302, $headers = [], $secure = null)
    {
        if (is_null($to)) {
            return self::getRedirector();
        }

        return self::getRedirector()->to($to, $status, $headers, $secure);
    }

    /**
     * @return Redirector
     */
    public static function getRedirector(): Redirector
    {
        if (!isset(self::$redirector)) {
            self::$redirector = Container::getInstance()->make('redirect');
        }

        return self::$redirector;
    }
}
