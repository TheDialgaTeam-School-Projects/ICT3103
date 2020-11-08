<?php
/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection PhpDocMissingThrowsInspection */

namespace App\Helpers\Traits;

use Illuminate\Container\Container;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\Contracts\View\View;

trait ViewHelperTrait
{
    use SessionHelperTrait;

    /**
     * @var ViewFactory
     */
    private static $view;

    /**
     * Get the evaluated view contents for the given view.
     *
     * @param string|null $view
     * @param Arrayable|array $data
     * @param array $mergeData
     * @return View|ViewFactory
     */
    public static function view(string $view, array $data = [], $mergeData = []): View
    {
        $factory = self::getView();

        if (func_num_args() === 0) {
            return $factory;
        }

        return $factory->make($view, $data, array_merge([
            'alertType' => self::getSession()->get('alertType'),
            'alertMessage' => self::getSession()->get('alertMessage'),
        ], $mergeData));
    }

    /**
     * @return ViewFactory
     */
    public static function getView(): ViewFactory
    {
        if (!isset(self::$view)) {
            self::$view = Container::getInstance()->make(ViewFactory::class);
        }

        return self::$view;
    }
}
