<?php

namespace teamwork\core\router;

use InvalidArgumentException;
use ReflectionClass;
use teamwork\core\application\ApplicationInterface;
use teamwork\core\controller\ControllerInterface;
use teamwork\core\exception\router\RouteNotFoundException;
use teamwork\core\response\ResponseInterface;

/**
 * Class for handling route.
 * @package teamwork\core
 */
class Router implements RouterInterface
{
    /** @var Route[] Array of registered route. */
    private array $routes = [];

    /**
     * Router constructor.
     * @param array $routeConfig Route configuration.
     */
    public function __construct(array $routeConfig)
    {
        foreach ($routeConfig as $routeName => $routeInfo) {
            $this->routes[] = new Route($routeName, $routeInfo['Route'], $routeInfo['Controller'], $routeInfo['Action']);
        }
    }

    public function findAndExecuteRoute(ApplicationInterface $app): void
    {
        /** @var Route $foundRoute */
        $foundRoute = null;

        foreach ($this->routes as $route) {
            if (!$route->isRouteMatch($app->getRequest()->getUri())) continue;
            $foundRoute = $route;
            break;
        }

        if (!$foundRoute) throw new RouteNotFoundException($app->getRequest()->getUri());

        $controllerClassPath = sprintf('teamwork\\controller\\%s', $foundRoute->getTargetController());
        /** @var ControllerInterface $controllerObject */
        $controllerObject = new $controllerClassPath($app);
        $reflector = new ReflectionClass($controllerObject);
        $controllerAction = $reflector->getMethod($app->getConfig()['Default']['ActionPrefix'] . $foundRoute->getTargetAction());
        $requiredParametersCount = $controllerAction->getNumberOfRequiredParameters();

        $response = $controllerObject->beforeDispatch();

        if ($response instanceof ResponseInterface) {
            $response->executeResponse();
            return;
        }

        if ($requiredParametersCount === 0) {
            // No parameter accepted for this action.
            $response = $controllerAction->invoke($controllerObject);
        } else if ($requiredParametersCount === 1) {
            $parameters = [];
            $requestUriParts = explode('/', $app->getRequest()->getUri());
            $requestUriPartsCount = count($requestUriParts);
            $routeUriParts = $foundRoute->getUriParts();

            for ($i = 0; $i < $requestUriPartsCount; $i++) {
                if (!$routeUriParts[$i]['isNamedParameter']) continue;
                $parameters[$routeUriParts[$i]['value']] = $requestUriParts[$i];
            }

            $response = $controllerAction->invokeArgs($controllerObject, [$parameters]);
        } else {
            throw new InvalidArgumentException(sprintf('Too many parameters in %s%s function in %s.', $app->getConfig()['Default']['ActionPrefix'], $foundRoute->getTargetAction(), $foundRoute->getTargetController()));
        }

        if ($response instanceof ResponseInterface) {
            $response->executeResponse();
        }

        $controllerObject->afterDispatch();
    }

    public function getRouteUri(string $routeName, ...$params): string
    {
        foreach ($this->routes as $route) {
            if ($route->getName() !== $routeName) continue;
            return $route->getRouteUri(...$params);
        }

        throw new RouteNotFoundException($routeName);
    }
}
