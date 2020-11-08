<?php
/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection PhpDocMissingThrowsInspection */

namespace App\Helpers\Traits;

use Illuminate\Config\Repository;
use Illuminate\Container\Container;

trait ConfigHelperTrait
{
    /**
     * @var Repository
     */
    private static $config;

    /**
     * Get / set the specified configuration value.
     *
     * If an array is passed as the key, we will assume you want to set an array of values.
     *
     * @param array|string|null $key
     * @param mixed $default
     * @return mixed|Repository
     */
    public static function config($key = null, $default = null)
    {
        if (is_null($key)) {
            return self::getConfig();
        }

        if (is_array($key)) {
            self::getConfig()->set($key);
        }

        return self::getConfig()->get($key, $default);
    }

    /**
     * @return Repository
     */
    public static function getConfig(): Repository
    {
        if (!isset(self::$config)) {
            self::$config = Container::getInstance()->make('config');
        }

        return self::$config;
    }
}
