<?php

namespace teamwork\core;

class Request
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

    /**
     * Get the Uri portion of the request.
     * @return string Request uri.
     */
    public function getUri(): string
    {
        return $this->uri;
    }

    /**
     * Get the request method.
     * @return string Request method.
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * Get if the request method is get.
     * @return bool true if request method is get else false.
     */
    public function isGet(): bool
    {
        return $this->method === 'GET';
    }

    /**
     * Get if the request method is post.
     * @return bool true if request method is post else false.
     */
    public function isPost(): bool
    {
        return $this->method === 'POST';
    }

    /**
     * Get if the request method is put.
     * @return bool true if request method is put else false.
     */
    public function isPut(): bool
    {
        return $this->method === 'PUT';
    }

    /**
     * Get if the request method is head.
     * @return bool true if request method is head else false.
     */
    public function isHead(): bool
    {
        return $this->method === 'HEAD';
    }
}
