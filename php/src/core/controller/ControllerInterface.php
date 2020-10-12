<?php

namespace teamwork\core\controller;

use Exception;
use teamwork\core\response\ResponseInterface;

/**
 * Interface for a generic controller.
 * @package teamwork\core\controller
 */
interface ControllerInterface
{
    /**
     * This method execute before dispatching this page.
     * @return ResponseInterface response object.
     * @throws Exception response error.
     */
    public function beforeDispatch(): ?ResponseInterface;

    /**
     * This method execute after dispatching this page.
     * @throws Exception response error.
     */
    public function afterDispatch(): void;
}
