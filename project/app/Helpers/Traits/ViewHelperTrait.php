<?php
/** @noinspection PhpUnhandledExceptionInspection */

/** @noinspection PhpDocMissingThrowsInspection */

namespace App\Helpers\Traits;

use Illuminate\Container\Container;
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
     * @param string $view
     * @param array $data
     * @return View
     */
    public static function view(string $view, array $data = []): View
    {
        return self::getView()->make($view, $data);
    }

    /**
     * Get the evaluated view contents with alert message for the given view.
     *
     * @param string $view
     * @param array $data
     * @return View
     */
    public static function viewWithAlertMessage(string $view, array $data = []): View
    {
        return self::getView()->make($view, array_merge([
            'alertType' => self::getSession()->get('alertType'),
            'alertMessage' => self::getSession()->get('alertMessage'),
        ], $data));
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