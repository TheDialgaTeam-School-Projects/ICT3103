<?php
/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection PhpDocMissingThrowsInspection */

namespace App\Helpers\Traits;

use Countable;
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
     * @param array  $replace
     * @param string|null $locale
     * @return string|array|null
     */
    public static function __($key = null, array $replace = [], $locale = null)
    {
        if (is_null($key)) {
            return $key;
        }

        return self::getTranslator()->get($key, $replace, $locale);
    }

    /**
     * Translates the given message based on a count.
     *
     * @param string $key
     * @param Countable|int|array $number
     * @param array $replace
     * @param string|null $locale
     * @return string
     */
    public static function trans_choice(string $key, $number, array $replace = [], $locale = null): string
    {
        return self::getTranslator()->choice($key, $number, $replace, $locale);
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
