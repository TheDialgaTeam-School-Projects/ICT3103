<?php
/** @noinspection PhpUnhandledExceptionInspection */

/** @noinspection PhpDocMissingThrowsInspection */

namespace App\Helpers\Traits;

use Illuminate\Container\Container;
use Illuminate\Contracts\Routing\UrlGenerator;

trait RouteHelperTrait
{
    /**
     * @var UrlGenerator
     */
    private static $route;

    /**
     * Generate the URL to a controller action.
     *
     * @param string|array $name
     * @param mixed $parameters
     * @param bool $absolute
     * @return string
     */
    public static function action($name, $parameters = [], $absolute = true): string
    {
        return self::getRoute()->action($name, $parameters, $absolute);
    }

    /**
     * Generate an asset path for the application.
     *
     * @param string $path
     * @param bool|null $secure
     * @return string
     */
    public static function asset(string $path, $secure = null): string
    {
        return self::getRoute()->asset($path, $secure);
    }

    /**
     * Generate the URL to a named route.
     *
     * @param array|string $name
     * @param array $parameters
     * @param bool $absolute
     * @return string
     */
    public static function route($name, $parameters = [], $absolute = true): string
    {
        return self::getRoute()->route($name, $parameters, $absolute);
    }

    /**
     * Generate an asset path for the application.
     *
     * @param string $path
     * @return string
     */
    public static function secure_asset(string $path): string
    {
        return self::asset($path, true);
    }

    /**
     * Generate a HTTPS url for the application.
     *
     * @param string $path
     * @param mixed $parameters
     * @return string
     */
    public static function secure_url(string $path, $parameters = [])
    {
        return self::url($path, $parameters, true);
    }

    /**
     * Generate a url for the application.
     *
     * @param string|null $path
     * @param mixed $parameters
     * @param bool|null $secure
     * @return UrlGenerator|string
     */
    public static function url($path = null, $parameters = [], $secure = null)
    {
        if (is_null($path)) {
            return self::getRoute();
        }

        return self::getRoute()->to($path, $parameters, $secure);
    }

    /**
     * @return UrlGenerator
     */
    public static function getRoute(): UrlGenerator
    {
        if (!isset(self::$route)) {
            self::$route = Container::getInstance()->make('url');
        }

        return self::$route;
    }
}
