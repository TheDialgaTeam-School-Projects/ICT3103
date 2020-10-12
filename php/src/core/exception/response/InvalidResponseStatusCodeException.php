<?php

namespace teamwork\core\exception\response;

use Exception;
use Throwable;

/**
 * Class for throwing invalid response status code exception.
 * @package teamwork\core\exception
 */
class InvalidResponseStatusCodeException extends Exception
{
    /**
     * InvalidStatusCodeException constructor.
     * @param int $statusCode Response http status code.
     * @param int $code The Exception code.
     * @param Throwable|null $previous The previous throwable used for the exception chaining.
     */
    public function __construct(int $statusCode, $code = 0, Throwable $previous = null)
    {
        parent::__construct(sprintf('%d is not a valid response status code.', $statusCode), $code, $previous);
    }
}
