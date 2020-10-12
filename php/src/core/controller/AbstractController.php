<?php

namespace teamwork\core\controller;

use teamwork\core\application\Application;
use teamwork\core\application\ApplicationInterface;
use teamwork\core\exception\router\RouteNotFoundException;
use teamwork\core\response\RedirectResponse;
use teamwork\core\response\ResponseInterface;
use teamwork\core\response\view\HtmlViewResponse;

abstract class AbstractController implements ControllerInterface
{
    /** @var ApplicationInterface Application object. */
    private ApplicationInterface $app;

    /**
     * Controller constructor.
     * @param ApplicationInterface $app Application object.
     */
    public function __construct(ApplicationInterface $app)
    {
        $this->app = $app;
    }

    public function beforeDispatch(): ?ResponseInterface
    {
        return null;
    }

    public function afterDispatch(): void
    {
    }

    /**
     * Get application object.
     * @return Application application object.
     */
    protected function getApp(): ApplicationInterface
    {
        return $this->app;
    }

    /**
     * Get if the request is GET method.
     * @return bool true if the request is GET method, else false.
     */
    protected function isGetMethod(): bool
    {
        return $this->app->getRequest()->isGetMethod();
    }

    /**
     * Get if the request is POST method.
     * @return bool true if the request is POST method, else false.
     */
    protected function isPostMethod(): bool
    {
        return $this->app->getRequest()->isPostMethod();
    }

    /**
     * Get if the request is PUT method.
     * @return bool true if the request is PUT method, else false.
     */
    protected function isPutMethod(): bool
    {
        return $this->app->getRequest()->isPutMethod();
    }

    /**
     * Get if the request is HEAD method.
     * @return bool true if the request is head method, else false.
     */
    protected function isHeadMethod(): bool
    {
        return $this->app->getRequest()->isHeadMethod();
    }

    /**
     * Get route uri from route name.
     * @param string $routeName Route name.
     * @param mixed ...$params Route parameters.
     * @return string route uri.
     * @throws RouteNotFoundException route found not be found.
     */
    protected function getRouteUri(string $routeName, ...$params): string
    {
        return $this->app->getRouter()->getRouteUri($routeName, ...$params);
    }

    /**
     * Pipe the request to a view and return the response object.
     * @param string $viewTemplatePath Relative path from view folder for the view template.
     * @param array $params Key-value pair parameters.
     * @return ResponseInterface response object.
     */
    protected function view(string $viewTemplatePath, $params = []): ResponseInterface
    {
        return new HtmlViewResponse($this->app, $viewTemplatePath, $params);
    }

    /**
     * Pipe the request to an error view and return the response.
     * @param string $message Error message.
     * @param int $responseCode Response code.
     * @return ResponseInterface response object.
     */
    protected function error(string $message, int $responseCode = ResponseInterface::RESPONSE_STATUS_CODE_BAD_REQUEST): ResponseInterface
    {
        return new HtmlViewResponse($this->app, $this->app->getConfig()['Default']['ErrorViewTemplatePath'], [
            'pageTitle' => 'Oops! We ran into some problems.',
            'errorMessage' => $message
        ], ResponseInterface::RESPONSE_STATUS_CODE_BAD_REQUEST);
    }

    /**
     * Pipe the request to redirect to another page.
     * @param string $uri Route to redirect.
     * @param int $statusCode Redirect status code.
     * @return ResponseInterface response object.
     */
    protected function redirect(string $uri, int $statusCode): ResponseInterface
    {
        return new RedirectResponse($this->app->getRequest(), $uri, $statusCode);
    }
}
