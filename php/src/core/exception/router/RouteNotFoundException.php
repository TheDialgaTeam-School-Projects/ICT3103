<?php

namespace teamwork\core\exception\router;

use Exception;
use Throwable;

/**
 * Class for throwing route not found exception.
 * @package teamwork\core\exception
 */
class RouteNotFoundException extends Exception
{
    /**
     * RouteNotFoundException constructor.
     * @param string $routeName Route name.
     * @param int $code The Exception code.
     * @param Throwable|null $previous The previous throwable used for the exception chaining.
     */
    public function __construct(string $routeName, int $code = 0, Throwable $previous = null)
    {
        parent::__construct(sprintf('Route %s could not be found.', $routeName), $code, $previous);
    }
}
