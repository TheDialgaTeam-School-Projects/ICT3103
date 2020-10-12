<?php

namespace teamwork\core\response;

/**
 * Interface for redirect response.
 * @package teamwork\core\response
 */
interface RedirectResponseInterface extends ResponseInterface
{
    /**
     * Get the uri to redirect.
     * @return string uri to redirect.
     */
    public function getUri(): string;
}
