<?php

namespace App\Services;

use Countable;
use Illuminate\Contracts\Translation\Translator;

abstract class Service
{
    /**
     * @var Translator
     */
    private $translator;

    protected function __construct(Translator $translator)
    {
        $this->translator = $translator;
    }

    /**
     * Translate the given message.
     *
     * @param string $key
     * @param array $replace
     * @param string|null $locale
     * @return mixed
     */
    protected function __(string $key, array $replace = [], string $locale = null)
    {
        return $this->translator->get($key, $replace, $locale);
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
    protected function trans_choice(string $key, $number, array $replace = [], string $locale = null): string
    {
        return $this->translator->choice($key, $number, $replace, $locale);
    }
}
