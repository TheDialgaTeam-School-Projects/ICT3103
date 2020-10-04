<?php

namespace teamwork\core\ViewRenderer;

use Exception;
use teamwork\core\App;

class HtmlViewRenderer extends AbstractViewRenderer
{
    /** @var App Application. */
    private App $app;

    /** @var string View template path. */
    private string $viewTemplatePath;

    /**
     * HtmlViewRenderer constructor.
     * @param App $app Application object.
     * @param string $viewTemplatePath View template path.
     * @param array $viewParameters View template parameters.
     */
    public function __construct(App $app, string $viewTemplatePath, array $viewParameters)
    {
        parent::__construct($viewParameters);
        $this->app = $app;
        $this->viewTemplatePath = $viewTemplatePath;
    }

    public function Render(): void
    {
        $pageViewTemplatePath = false;

        if (strpos($this->viewTemplatePath, '/') !== false) {
            $pageViewTemplatePath = realpath(__DIR__ . '/../../view/' . $this->app->getConfig()['Default']['PageViewTemplatePath']);
        }

        if ($pageViewTemplatePath === false) {
            $pageViewTemplatePath = realpath(__DIR__ . '/../../view/' . $this->app->getConfig()['Default']['PageViewTemplatePath']);
        }

        if ($pageViewTemplatePath === false) throw new Exception($this->app->getConfig()['Default']['PageViewTemplatePath'] . ' is missing.');

        require $pageViewTemplatePath;
    }

    public function __get($name)
    {
        if (array_key_exists($name, $this->viewParameters)) return $this->viewParameters[$name];
        return $this->$name;
    }

    public function __isset($name)
    {
        if (array_key_exists($name, $this->viewParameters)) return true;
        return isset($this->$name);
    }

    /**
     * Get route uri.
     * @param string $routeName Route name.
     * @param mixed ...$params Route parameters.
     * @return string Route uri.
     */
    public function getRouteUri(string $routeName, ...$params): string
    {
        return $this->app->getRouter()->getRouteUri($routeName, ...$params);
    }

    /**
     * Get absolute view template path.
     * @return string View template path.
     */
    public function getAbsoluteViewTemplatePath(): string
    {
        if (substr($this->viewTemplatePath, -4) === '.php') {
            return realpath(__DIR__ . '/../../view/' . $this->viewTemplatePath);
        } else {
            return realpath(__DIR__ . '/../../view/' . $this->viewTemplatePath . '.php');
        }
    }

    /**
     * Get a random csrf token.
     * @param string $sessionKey Session key used to store csrf token.
     * @param bool $regenerate Whether to regenerate the csrf token.
     * @return string Csrf token.
     */
    public function getCsrfToken(string $sessionKey = 'csrfToken', bool $regenerate = false): string
    {
        return $this->app->getCsrfTokenService()->getCsrfToken($sessionKey, $regenerate);
    }
}
