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
