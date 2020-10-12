<?php

namespace teamwork\core\response;

use teamwork\core\exception\response\InvalidResponseStatusCodeException;
use teamwork\core\request\RequestInterface;
use teamwork\core\response\view\ViewResponseInterface;

/**
 * Class for outgoing response from server to client.
 * @package teamwork\core
 */
abstract class AbstractResponse implements ResponseInterface
{
    private const RESPONSE_STATUS_CODES = [
        200 => '200 OK',
        201 => '201 Created',
        301 => '301 Moved Permanently',
        302 => '302 Found',
        303 => '303 See Other',
        307 => '307 Temporary Redirect',
        308 => '308 Permanent Redirect',
        400 => '400 Bad Request',
        403 => '403 Forbidden',
        404 => '404 Not Found',
        500 => '500 Internal Server Error',
        501 => '501 Not Implemented',
    ];

    /** @var RequestInterface Request object. */
    private RequestInterface $request;

    /** @var array Response headers. */
    private array $headers = [];

    /**
     * AbstractResponse constructor.
     * @param RequestInterface $request Request object.
     */
    protected function __construct(RequestInterface $request)
    {
        $this->request = $request;
    }

    public function executeResponse(): void
    {
        if (!array_key_exists($this->getStatusCode(), self::RESPONSE_STATUS_CODES)) throw new InvalidResponseStatusCodeException($this->getStatusCode());
        header(sprintf('%s %s', $_SERVER['SERVER_PROTOCOL'], self::RESPONSE_STATUS_CODES[$this->getStatusCode()]));

        foreach ($this->headers as $header) {
            header($header);
        }

        // If the request is HEAD, it should ignore the body.
        if ($this->request->isHeadMethod()) return;

        if ($this instanceof RedirectResponseInterface) {
            header(sprintf('Location: %s', $this->getUri()));
            return;
        } else if ($this instanceof ViewResponseInterface) {
            $this->render();
        }
    }

    /**
     * Get the status code of the response.
     * @return int status code of the response.
     */
    protected abstract function getStatusCode(): int;

    /**
     * Set response header.
     * @param string $key Response header key.
     * @param string $value Response header value.
     */
    protected function setResponseHeader(string $key, string $value): void
    {
        $this->headers[$key] = $value;
    }
}
