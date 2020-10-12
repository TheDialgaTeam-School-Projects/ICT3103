<?php

namespace teamwork\core\application;

use Exception;
use InvalidArgumentException;
use ReflectionException;
use teamwork\core\exception\response\InvalidResponseStatusCodeException;
use teamwork\core\exception\response\view\ViewTemplateNotFoundException;
use teamwork\core\exception\router\RouteNotFoundException;
use teamwork\core\request\Request;
use teamwork\core\request\RequestInterface;
use teamwork\core\response\ResponseInterface;
use teamwork\core\response\view\HtmlViewResponse;
use teamwork\core\router\Router;
use teamwork\core\router\RouterInterface;
use Throwable;

/**
 * Class for main application workflow.
 * @package teamwork\core
 */
class Application implements ApplicationInterface
{
    /** @var array App configuration. */
    private array $config;

    /** @var RequestInterface Http request information. */
    private RequestInterface $request;

    /** @var RouterInterface App router. */
    private RouterInterface $router;

    public function start(string $configPath): void
    {
        set_exception_handler([$this, 'exceptionHandler']);
        session_start();

        $this->config = require $configPath;
        $this->request = new Request($_SERVER["REQUEST_URI"], $_SERVER["REQUEST_METHOD"]);
        $this->router = new Router($this->config['Routes']);

        try {
            $this->router->findAndExecuteRoute($this);
        } catch (RouteNotFoundException $e) {
            // Route could not be found.
            $this->error(ResponseInterface::RESPONSE_STATUS_CODE_NOT_FOUND, $e->getMessage(), $e->getTraceAsString());
        } catch (ViewTemplateNotFoundException $e) {
            // View template could not be found.
            $this->error(ResponseInterface::RESPONSE_STATUS_CODE_NOT_FOUND, $e->getMessage(), $e->getTraceAsString());
        } catch (Exception $e) {
            // Response error.
            $this->error(ResponseInterface::RESPONSE_STATUS_CODE_INTERNAL_SERVER_ERROR, $e->getMessage(), $e->getTraceAsString());
        }
    }

    /**
     * Pipe the error into response.
     * @param int $errorCode Error status code.
     * @param string $message Error message.
     * @param string|null $stacktrace Error stacktrace.
     */
    private function error(int $errorCode, string $message, string $stacktrace = null): void
    {
        try {
            $response = new HtmlViewResponse($this, $this->config['Default']['ErrorViewTemplatePath'], [
                'pageTitle' => 'Oops! We ran into some problems.',
                'errorMessage' => $this->config['Development']['ShowError'] ? $message : 'Unexpected error occurred.',
                'errorStackTrace' => $this->config['Development']['ShowStackTrace'] ? $stacktrace : '',
            ], $errorCode);
            $response->executeResponse();
        } catch (Exception $e) {
            echo '<h1>Oops! We ran into some problems.</h1>';
            echo '<p>' . ($this->config['Development']['ShowError'] ? $e->getMessage() : 'Unexpected error occurred.') . '</p>';
            array_map(function ($value) {
                echo "<p>$value</p>";
            }, preg_split(sprintf('/%s/', PHP_EOL), $this->config['Development']['ShowStackTrace'] ? $e->getTraceAsString() : ''));
        }
    }

    /**
     * Fail over exception handler.
     * @param Throwable $exception Exception thrown.
     */
    public function exceptionHandler(Throwable $exception)
    {
        try {
            $response = new HtmlViewResponse($this, $this->config['Default']['ErrorViewTemplatePath'], [
                'pageTitle' => 'Oops! We ran into some problems.',
                'errorMessage' => $this->config['Development']['ShowError'] ? $exception->getMessage() : 'Unexpected error occurred.',
                'errorStackTrace' => $this->config['Development']['ShowStackTrace'] ? $exception->getTraceAsString() : '',
            ], ResponseInterface::RESPONSE_STATUS_CODE_INTERNAL_SERVER_ERROR);
            $response->executeResponse();
        } catch (Throwable $e) {
            echo '<h1>Oops! We ran into some problems.</h1>';
            echo '<p>' . ($this->config['Development']['ShowError'] ? $exception->getMessage() : 'Unexpected error occurred.') . '</p>';
            array_map(function ($value) {
                echo "<p>$value</p>";
            }, preg_split(sprintf('/%s/', PHP_EOL), $this->config['Development']['ShowStackTrace'] ? $exception->getTraceAsString() : ''));
        }
        die(ResponseInterface::RESPONSE_STATUS_CODE_INTERNAL_SERVER_ERROR);
    }

    public function getConfig(): array
    {
        return $this->config;
    }

    public function getRequest(): RequestInterface
    {
        return $this->request;
    }

    public function getRouter(): RouterInterface
    {
        return $this->router;
    }
}
