<?php

namespace teamwork\core\request;

/**
 * Interface for incoming request from the client to the server.
 * @package teamwork\core
 */
interface RequestInterface
{
    /**
     * Get the Uri portion of the request.
     * @return string request uri.
     */
    public function getUri(): string;

    /**
     * Get the request method.
     * @return string request method.
     */
    public function getMethod(): string;

    /**
     * Get if the request method is get.
     * @return bool true if request method is get else false.
     */
    public function isGetMethod(): bool;

    /**
     * Get if the request method is post.
     * @return bool true if request method is post else false.
     */
    public function isPostMethod(): bool;

    /**
     * Get if the request method is put.
     * @return bool true if request method is put else false.
     */
    public function isPutMethod(): bool;

    /**
     * Get if the request method is head.
     * @return bool true if request method is head else false.
     */
    public function isHeadMethod(): bool;
}
