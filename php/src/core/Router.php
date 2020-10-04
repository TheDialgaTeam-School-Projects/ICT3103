<?php

namespace teamwork\core;

class Router
{
    /** @var Route[] Array of registered route. */
    private array $routes;

    /**
     * Add routes by configuration.
     * @param array $routeConfig Route configuration.
     */
    public function addRoutesByConfig(array $routeConfig): void
    {
        foreach ($routeConfig as $routeName => $routeInfo) {
            $this->routes[] = new Route($routeName, $routeInfo['Route'], $routeInfo['Controller'], $routeInfo['Action']);
        }
    }

    /**
     * Find matching route by http request.
     * @param Request $request Http request.
     * @return Route|null The matching route of the http request.
     */
    public function findRoute(Request $request): ?Route
    {
        foreach ($this->routes as $route) {
            if (!$route->isRouteMatch($request->getUri())) continue;
            return $route;
        }

        return null;
    }

    /**
     * Get route uri from route name.
     * @param string $routeName Route name.
     * @param mixed ...$params Route parameters.
     * @return string Route uri.
     */
    public function getRouteUri(string $routeName, ...$params): string
    {
        foreach ($this->routes as $route) {
            if ($route->getName() !== $routeName) continue;
            return $route->getRouteUri(...$params);
        }

        return '/';
    }
}
