<?php

namespace teamwork\core;

use teamwork\core\ViewRenderer\HtmlViewRenderer;

abstract class AbstractController
{
    /** @var App Application object. */
    private App $app;

    /** @var Response Response object. */
    private Response $response;

    /**
     * Controller constructor.
     * @param App $app Application object.
     */
    public function __construct(App $app)
    {
        $this->app = $app;
        $this->response = new Response($this->app->getRequest());
    }

    /**
     * This method execute before dispatching this page.
     */
    public function beforeDispatch(): void
    {
    }

    /**
     * This method execute after dispatching this page.
     */
    public function afterDispatch(): void
    {
    }

    /**
     * Get if the request is GET method.
     * @return bool true if the request is GET method, else false.
     */
    protected function isGet(): bool
    {
        return $this->app->getRequest()->isGet();
    }

    /**
     * Get if the request is POST method.
     * @return bool true if the request is POST method, else false.
     */
    protected function isPost(): bool
    {
        return $this->app->getRequest()->isPost();
    }

    /**
     * Get if the request is PUT method.
     * @return bool true if the request is PUT method, else false.
     */
    protected function isPut(): bool
    {
        return $this->app->getRequest()->isPut();
    }

    /**
     * Get if the request is HEAD method.
     * @return bool true if the request is head method, else false.
     */
    protected function isHead(): bool
    {
        return $this->app->getRequest()->isHead();
    }

    /**
     * Get route uri.
     * @param string $routeName Route name.
     * @param mixed ...$params Route parameters.
     * @return string Route uri.
     */
    protected function getRouteUri(string $routeName, ...$params): string
    {
        return $this->app->getRouter()->getRouteUri($routeName, ...$params);
    }

    /**
     * Get database object.
     * @return MySql Database object.
     */
    protected function getDb(): MySql
    {
        return $this->app->getDb();
    }

    /**
     * Get application object.
     * @return App Application object.
     */
    protected function getApp(): App
    {
        return $this->app;
    }

    /**
     * Pipe the request to a view and return the response.
     * @param string $viewTemplatePath Relative path from view folder for the view template.
     * @param array $params Key-value pair parameters.
     * @return Response Response object.
     */
    protected function view(string $viewTemplatePath, $params = []): Response
    {
        $this->response->setStatus(Response::RESPONSE_STATUS_CODE_OK);
        $this->response->setBody(new HtmlViewRenderer($this->app, $viewTemplatePath, $params));
        return $this->response;
    }

    /**
     * Pipe the request to an error view and return the response.
     * @param string $message Error message.
     * @param int $responseCode Response code.
     * @return Response Response object.
     */
    protected function error(string $message, int $responseCode = Response::RESPONSE_STATUS_CODE_BAD_REQUEST): Response
    {
        $this->response->setStatus($responseCode);
        $this->response->setBody(new HtmlViewRenderer($this->app, 'defaultErrorViewTemplate', [
            'pageTitle' => 'Oops! We ran into some problems.',
            'errorMessage' => $message
        ]));
        return $this->response;
    }

    /**
     * Pipe the request to redirect to another page.
     * @param string $routeUri Route to redirect.
     * @param int $statusCode Redirect status code.
     */
    protected function redirect(string $routeUri, int $statusCode): void
    {
        $this->response->setRedirect($routeUri, $statusCode);
    }
}
