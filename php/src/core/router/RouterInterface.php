<?php

namespace teamwork\core\router;

use Exception;
use InvalidArgumentException;
use ReflectionException;
use teamwork\core\application\ApplicationInterface;
use teamwork\core\exception\response\InvalidResponseStatusCodeException;
use teamwork\core\exception\response\view\ViewTemplateNotFoundException;
use teamwork\core\exception\router\RouteNotFoundException;

/**
 * Interface for handling route.
 * @package teamwork\core
 */
interface RouterInterface
{
    /**
     * Find the matching route and execute.
     * @param ApplicationInterface $app Application object.
     * @throws RouteNotFoundException route could not be found.
     * @throws ReflectionException class does not exist.
     * @throws InvalidResponseStatusCodeException invalid response status code given.
     * @throws ViewTemplateNotFoundException view template could not be found.
     * @throws InvalidArgumentException too much parameter exist in the controller function.
     */
    public function findAndExecuteRoute(ApplicationInterface $app): void;

    /**
     * Get route uri from route name.
     * @param string $routeName Route name.
     * @param mixed ...$params Route parameters.
     * @return string route uri.
     * @throws RouteNotFoundException route could not be found.
     */
    public function getRouteUri(string $routeName, ...$params): string;
}
