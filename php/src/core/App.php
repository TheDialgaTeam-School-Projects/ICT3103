<?php

namespace teamwork\core;

use Exception;
use ReflectionException;
use teamwork\core\ViewRenderer\HtmlViewRenderer;
use teamwork\service\CsrfTokenService;

class App
{
    /** @var array App configuration. */
    private array $config;

    /** @var Request Http request information. */
    private Request $request;

    /** @var Router App router. */
    private Router $router;

    /** @var MySql Database object. */
    private MySql $db;

    /** @var CsrfTokenService Csrf token service. */
    private CsrfTokenService $csrfTokenService;

    /**
     * Start application.
     * @param string $configPath Configuration path
     * @throws Exception Unexpected error occurred.
     */
    public function start(string $configPath)
    {
        set_exception_handler([$this, 'exceptionHandler']);
        session_start();

        $this->config = require $configPath;

        $this->request = new Request($_SERVER["REQUEST_URI"], $_SERVER["REQUEST_METHOD"]);

        $this->router = new Router();
        $this->router->addRoutesByConfig($this->config['Routes']);

        // Find matching routes.
        $matchingRoute = $this->router->findRoute($this->request);

        if ($matchingRoute !== null) {
            // Route exist.
            try {
                $matchingRoute->executeRoute($this);
            } catch (ReflectionException $e) {
                // Controller action does not exist.
                $this->error(Response::RESPONSE_STATUS_CODE_NOT_IMPLEMENTED, $this->config['Default']['ActionPrefix'] . $matchingRoute->getTargetAction() . ' does not exist in ' . $matchingRoute->getTargetController() . '.', $e->getTraceAsString())->execute();
            } catch (Exception $e) {
                // Some exception thrown during dispatching.
                $this->error(Response::RESPONSE_STATUS_CODE_INTERNAL_SERVER_ERROR, $e->getMessage(), $e->getTraceAsString())->execute();
            }
        } else {
            // Route does not exist.
            $this->error(Response::RESPONSE_STATUS_CODE_NOT_FOUND, 'The requested page could not be found.')->execute();
        }
    }

    /**
     * Pipe the error into response.
     * @param int $errorCode Error status code.
     * @param string $message Error message.
     * @param string|null $stacktrace Error stacktrace.
     * @return Response Response object.
     */
    private function error(int $errorCode, string $message, string $stacktrace = null): Response
    {
        $response = new Response($this->request);
        $response->setStatus($errorCode);
        $response->setBody(new HtmlViewRenderer($this, $this->config['Default']['ErrorViewTemplatePath'], [
            'pageTitle' => 'Oops! We ran into some problems.',
            'errorMessage' => $this->config['Development']['ShowError'] ? $message : 'Unexpected error occurred.',
            'errorStackTrace' => $this->config['Development']['ShowStackTrace'] ? $stacktrace : '',
        ]));
        return $response;
    }

    /**
     * Fail over exception handler.
     * @param Exception $exception Exception thrown.
     */
    private function exceptionHandler(Exception $exception)
    {
        $response = new Response($this->request);
        $response->setStatus(Response::RESPONSE_STATUS_CODE_INTERNAL_SERVER_ERROR);
        $response->setBody(new HtmlViewRenderer($this, $this->config['Default']['ErrorViewTemplatePath'], [
            'pageTitle' => 'Oops! We ran into some problems.',
            'errorMessage' => $this->config['Development']['ShowError'] ? $exception->getMessage() : 'Unexpected error occurred.',
            'errorStackTrace' => $this->config['Development']['ShowStackTrace'] ? $exception->getTraceAsString() : '',
        ]));
    }

    /**
     * Get application config.
     * @return array Application config.
     */
    public function getConfig(): array
    {
        return $this->config;
    }

    /**
     * Get the http request information.
     * @return Request Http request information.
     */
    public function getRequest(): Request
    {
        return $this->request;
    }

    /**
     * Get the app router.
     * @return Router App router.
     */
    public function getRouter(): Router
    {
        return $this->router;
    }

    /**
     * Get database object.
     * @return MySql database object.
     */
    public function getDb(): MySql
    {
        if (!isset($this->db)) $this->db = new MySql($this->config['Mysqli']);
        return $this->db;
    }

    /**
     * Get csrf token service.
     * @return CsrfTokenService Csrf token service.
     */
    public function getCsrfTokenService(): CsrfTokenService
    {
        if (!isset($this->csrfTokenService)) $this->csrfTokenService = new CsrfTokenService();
        return $this->csrfTokenService;
    }
}
