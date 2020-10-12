<?php

namespace teamwork\core\response\view;

use teamwork\core\request\RequestInterface;
use teamwork\core\response\AbstractResponse;

/**
 * Class for a generic view response.
 * @package teamwork\core\response\view
 */
abstract class AbstractViewResponse extends AbstractResponse implements ViewResponseInterface
{
    /** @var array View parameters. */
    protected array $viewParameters;

    /** @var int Status code. */
    private int $statusCode;

    /**
     * AbstractViewResponse constructor.
     * @param RequestInterface $request Request object.
     * @param array $viewParameters View parameters.
     * @param int $statusCode Status code.
     */
    protected function __construct(RequestInterface $request, array $viewParameters, int $statusCode = self::RESPONSE_STATUS_CODE_OK)
    {
        parent::__construct($request);
        $this->viewParameters = $viewParameters;
        $this->statusCode = $statusCode;
    }

    protected function getStatusCode(): int
    {
        return $this->statusCode;
    }
}
