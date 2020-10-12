<?php

namespace teamwork\core\exception\response\view;

use Exception;
use Throwable;

/**
 * Class for throwing view template not found exception.
 * @package teamwork\core\exception\response\view
 */
class ViewTemplateNotFoundException extends Exception
{
    /**
     * ViewTemplateNotFoundException constructor.
     * @param string $viewTemplateRelativeFilePath View template relative path.
     * @param int $code The Exception code.
     * @param Throwable|null $previous The previous throwable used for the exception chaining.
     */
    public function __construct(string $viewTemplateRelativeFilePath, int $code = 0, Throwable $previous = null)
    {
        parent::__construct(sprintf('View template could not be found in the view folder at %s.', $viewTemplateRelativeFilePath), $code, $previous);
    }
}
