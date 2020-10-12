<?php

namespace teamwork\core\request;

/**
 * Class for incoming request from the client to the server.
 * @package teamwork\core\request
 */
class Request implements RequestInterface
{
    /** @var string Request uri. */
    private string $uri;

    /** @var string Request method. */
    private string $method;

    /**
     * Request constructor.
     * @param string $uri Request uri.
     * @param string $method Request method.
     */
    public function __construct(string $uri, string $method)
    {
        $this->uri = $uri;
        $this->method = $method;
    }

    public function getUri(): string
    {
        return $this->uri;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    function isGetMethod(): bool
    {
        return $this->method === 'GET';
    }

    function isPostMethod(): bool
    {
        return $this->method === 'POST';
    }

    function isPutMethod(): bool
    {
        return $this->method === 'PUT';
    }

    function isHeadMethod(): bool
    {
        return $this->method === 'HEAD';
    }
}
