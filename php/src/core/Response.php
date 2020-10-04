<?php

namespace teamwork\core;

use Exception;
use teamwork\core\ViewRenderer\AbstractViewRenderer;

class Response
{
    /** @var int The HTTP 200 OK success status response code indicates that the request has succeeded. */
    public const RESPONSE_STATUS_CODE_OK = 200;

    /** @var int The HTTP 201 Created success status response code indicates that the request has succeeded and has led to the creation of a resource. */
    public const RESPONSE_STATUS_CODE_CREATED = 201;

    /** @var int The HTTP 301 Moved Permanently redirect status response code indicates that the resource requested has been definitively moved to the URL given by the Location headers. */
    public const RESPONSE_STATUS_CODE_MOVED_PERMANENTLY = 301;

    /** @var int The HTTP 302 Found redirect status response code indicates that the resource requested has been temporarily moved to the URL given by the Location header. */
    public const RESPONSE_STATUS_CODE_FOUND = 302;

    /** @var int The HTTP 303 See Other redirect status response code indicates that the redirects don't link to the newly uploaded resources, but to another page (such as a confirmation page or an upload progress page). */
    public const RESPONSE_STATUS_CODE_SEE_OTHER = 303;

    /** @var int The HTTP 307 Temporary Redirect redirect status response code indicates that the resource requested has been temporarily moved to the URL given by the Location headers. */
    public const RESPONSE_STATUS_CODE_TEMPORARY_REDIRECT = 307;

    /** @var int The HTTP 308 Permanent Redirect redirect status response code indicates that the resource requested has been definitively moved to the URL given by the Location headers. */
    public const RESPONSE_STATUS_CODE_PERMANENT_REDIRECT = 308;

    /** @var int The HTTP 400 Bad Request response status code indicates that the server cannot or will not process the request due to something that is perceived to be a client error (e.g., malformed request syntax, invalid request message framing, or deceptive request routing). */
    public const RESPONSE_STATUS_CODE_BAD_REQUEST = 400;

    /** @var int The HTTP 403 Forbidden client error status response code indicates that the server understood the request but refuses to authorize it. */
    public const RESPONSE_STATUS_CODE_FORBIDDEN = 403;

    /** @var int The HTTP 404 Not Found client error response code indicates that the server can't find the requested resource. */
    public const RESPONSE_STATUS_CODE_NOT_FOUND = 404;

    /** @var int The HTTP 500 Internal Server Error server error response code indicates that the server encountered an unexpected condition that prevented it from fulfilling the request. */
    public const RESPONSE_STATUS_CODE_INTERNAL_SERVER_ERROR = 500;

    /** @var int The HTTP 501 Not Implemented server error response code means that the server does not support the functionality required to fulfill the request. */
    public const RESPONSE_STATUS_CODE_NOT_IMPLEMENTED = 501;

    /** @var Request Request object. */
    private Request $request;

    /** @var string Response status. */
    private string $status;

    /** @var string[] Response headers. */
    private array $headers = [];

    /** @var mixed Response body. */
    private AbstractViewRenderer $body;

    /**
     * Response constructor.
     * @param Request $request Request object.
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Set response status code.
     * @param int $statusCode Response Status code.
     */
    public function setStatus(int $statusCode): void
    {
        switch ($statusCode) {
            case self::RESPONSE_STATUS_CODE_OK:
                $this->status = '200 OK';
                break;

            case self::RESPONSE_STATUS_CODE_CREATED:
                $this->status = '201 Created';
                break;

            case self::RESPONSE_STATUS_CODE_MOVED_PERMANENTLY:
                $this->status = '301 Moved Permanently';
                break;

            case self::RESPONSE_STATUS_CODE_FOUND:
                $this->status = '302 Found';
                break;

            case self::RESPONSE_STATUS_CODE_SEE_OTHER:
                $this->status = '303 See Other';
                break;

            case self::RESPONSE_STATUS_CODE_TEMPORARY_REDIRECT:
                $this->status = '307 Temporary Redirect';
                break;

            case self::RESPONSE_STATUS_CODE_PERMANENT_REDIRECT:
                $this->status = '308 Permanent Redirect';
                break;

            case self::RESPONSE_STATUS_CODE_BAD_REQUEST:
                $this->status = '400 Bad Request';
                break;

            case self::RESPONSE_STATUS_CODE_FORBIDDEN:
                $this->status = '403 Forbidden';
                break;

            case self::RESPONSE_STATUS_CODE_NOT_FOUND:
                $this->status = '404 Not Found';
                break;

            case self::RESPONSE_STATUS_CODE_INTERNAL_SERVER_ERROR:
                $this->status = '500 Internal Server Error';
                break;

            case self::RESPONSE_STATUS_CODE_NOT_IMPLEMENTED:
                $this->status = '501 Not Implemented';
                break;
        }
    }

    /**
     * Set response body.
     * @param mixed $body Response body.
     */
    public function setBody($body): void
    {
        $this->body = $body;
    }

    /**
     * Set redirect response.
     * @param string $uri Uri to redirect.
     * @param int $statusCode Redirect status code.
     */
    public function setRedirect(string $uri, int $statusCode): void
    {
        $this->setStatus($statusCode);
        $this->headers[] = 'Location: ' . $uri;
    }

    /**
     * Execute the response.
     * @throws Exception response had an error.
     */
    public function execute(): void
    {
        header($_SERVER['SERVER_PROTOCOL'] . ' ' . $this->status);

        foreach ($this->headers as $header) {
            header($header);
        }

        // If the request is HEAD, it should ignore the body.
        if ($this->request->isHead()) return;

        // If the body is empty, most likely we don't have to care below.
        if (empty($this->body)) return;

        if ($this->body instanceof AbstractViewRenderer) {
            $this->body->Render();
        } else {
            echo $this->body;
        }
    }
}
