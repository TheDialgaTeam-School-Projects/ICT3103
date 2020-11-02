<?php
/** @noinspection PhpUnhandledExceptionInspection */

/** @noinspection PhpDocMissingThrowsInspection */

namespace App\Helpers\Traits;

use Illuminate\Container\Container;
use Illuminate\Contracts\Translation\Translator;

trait TranslatorHelperTrait
{
    /**
     * @var Translator
     */
    private static $translator;

    /**
     * Translate the given message.
     *
     * @param string|null $key
     * @param array $replace
     * @return string|array|null
     */
    public static function __(string $key, $replace = [])
    {
        return self::getTranslator()->get($key, $replace);
    }

    /**
     * @return Translator
     */
    public static function getTranslator(): Translator
    {
        if (!isset(self::$translator)) {
            self::$translator = Container::getInstance()->make('translator');
        }

        return self::$translator;
    }
}
