<?php

namespace teamwork\core\application;

use teamwork\core\request\RequestInterface;
use teamwork\core\router\RouterInterface;

/**
 * Interface for the main application workflow.
 * @package teamwork\core
 */
interface ApplicationInterface
{
    /**
     * Start application.
     * @param string $configPath Configuration path.
     */
    public function start(string $configPath): void;

    /**
     * Get application configuration.
     * @return array application configuration.
     */
    public function getConfig(): array;

    /**
     * Get request object.
     * @return RequestInterface request object.
     */
    public function getRequest(): RequestInterface;

    /**
     * Get router object.
     * @return RouterInterface router object.
     */
    public function getRouter(): RouterInterface;
}
