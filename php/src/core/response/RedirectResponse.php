<?php

namespace teamwork\core\response;

use teamwork\core\request\RequestInterface;

/**
 * Class for redirect response.
 * @package teamwork\core\response
 */
class RedirectResponse extends AbstractResponse implements RedirectResponseInterface
{
    /** @var string Target route uri for redirect. */
    private string $uri;

    /** @var int Target status code for redirect. */
    private int $statusCode;

    /**
     * RedirectResponse constructor.
     * @param RequestInterface $request Request object.
     * @param string $uri Target route uri.
     * @param int $statusCode Status code for redirect.
     */
    public function __construct(RequestInterface $request, string $uri, int $statusCode)
    {
        parent::__construct($request);
        $this->uri = $uri;
        $this->statusCode = $statusCode;
    }

    public function getUri(): string
    {
        return $this->uri;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
}
