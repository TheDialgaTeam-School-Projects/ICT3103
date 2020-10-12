<?php

namespace teamwork\core\response\view;

use teamwork\core\application\ApplicationInterface;
use teamwork\core\exception\response\view\ViewPropertyNotFoundException;
use teamwork\core\exception\response\view\ViewTemplateNotFoundException;
use teamwork\core\exception\router\RouteNotFoundException;

/**
 * Class for html view response.
 * @package teamwork\core\response\view
 */
class HtmlViewResponse extends AbstractViewResponse
{
    /** @var string Relative path to view. */
    private const RELATIVE_PATH_TO_VIEW = __DIR__ . '/../../../view';

    /** @var ApplicationInterface Application object. */
    private ApplicationInterface $app;

    /** @var string View template path. */
    private string $viewTemplatePath;

    /**
     * HtmlViewRenderer constructor.
     * @param ApplicationInterface $app Application object.
     * @param string $viewTemplatePath View template path.
     * @param array $viewParameters View template parameters.
     * @param int $statusCode Status code.
     */
    public function __construct(ApplicationInterface $app, string $viewTemplatePath, array $viewParameters, int $statusCode = self::RESPONSE_STATUS_CODE_OK)
    {
        parent::__construct($app->getRequest(), $viewParameters, $statusCode);
        $this->app = $app;
        $this->viewTemplatePath = $viewTemplatePath;
    }

    public function render(): void
    {
        $defaultViewTemplatePath = $this->serializePhpPath($this->app->getConfig()['Default']['PageViewTemplatePath']);
        $pageViewTemplatePath = false;

        if (strpos($this->viewTemplatePath, '/') !== false) {
            $pageViewTemplatePath = realpath(sprintf('%s/%s/%s', self::RELATIVE_PATH_TO_VIEW, join('/', explode('/', $this->viewTemplatePath, -1)), $defaultViewTemplatePath));
        }

        if ($pageViewTemplatePath === false) {
            $pageViewTemplatePath = realpath(sprintf('%s/%s', self::RELATIVE_PATH_TO_VIEW, $defaultViewTemplatePath));
        }

        if ($pageViewTemplatePath === false) throw new ViewTemplateNotFoundException($defaultViewTemplatePath);

        require $pageViewTemplatePath;
    }

    /**
     * Serialize php file path.
     * @param string $phpFilePath Php file path to serialize.
     * @return string File path ending with .php
     */
    private function serializePhpPath(string $phpFilePath): string
    {
        if (substr($phpFilePath, -4) !== '.php') {
            return $phpFilePath . '.php';
        } else {
            return $phpFilePath;
        }
    }

    /**
     * Get absolute view template path.
     * @return string View template path.
     * @throws ViewTemplateNotFoundException view template could not be found.
     */
    public function getAbsoluteViewTemplatePath(): string
    {
        $relativeViewTemplateFilePath = $this->serializePhpPath(sprintf('%s/%s', self::RELATIVE_PATH_TO_VIEW, $this->viewTemplatePath));
        $absoluteViewTemplatePath = realpath($relativeViewTemplateFilePath);
        if ($absoluteViewTemplatePath === false) throw new ViewTemplateNotFoundException($relativeViewTemplateFilePath);
        return $absoluteViewTemplatePath;
    }

    /**
     * Get route uri from route name.
     * @param string $routeName Route name.
     * @param mixed ...$params Route parameters.
     * @return string route uri.
     * @throws RouteNotFoundException route could not be found.
     */
    public function getRouteUri(string $routeName, ...$params): string
    {
        return $this->app->getRouter()->getRouteUri($routeName, ...$params);
    }

    /**
     * Magic function for property getter.
     * @param string $name Name of the property to get.
     * @return mixed property value.
     * @throws ViewPropertyNotFoundException view property is not defined in the view.
     */
    public function __get(string $name)
    {
        if (array_key_exists($name, $this->viewParameters)) return $this->viewParameters[$name];
        throw new ViewPropertyNotFoundException($name);
    }

    /**
     * Magic function for checking if a property is defined.
     * @param string $name Name of the property to check.
     * @return bool true if property is defined.
     */
    public function __isset(string $name)
    {
        if (array_key_exists($name, $this->viewParameters)) {
            $value = $this->viewParameters[$name];
            return $value !== null && $value !== '\0';
        } else {
            return false;
        }
    }
}
