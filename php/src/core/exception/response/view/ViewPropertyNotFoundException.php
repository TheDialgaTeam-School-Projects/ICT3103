<?php

namespace teamwork\core\exception\response\view;

use Exception;
use Throwable;

/**
 * Class for throwing view property not found exception.
 * @package teamwork\core\exception\response\view
 */
class ViewPropertyNotFoundException extends Exception
{
    /**
     * ViewPropertyNotFoundException constructor.
     * @param string $propertyName Property name.
     * @param int $code The Exception code.
     * @param Throwable|null $previous The previous throwable used for the exception chaining.
     */
    public function __construct(string $propertyName, int $code = 0, Throwable $previous = null)
    {
        parent::__construct(sprintf('View property $this->%s is not defined in the view.', $propertyName), $code, $previous);
    }
}
