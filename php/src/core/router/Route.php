<?php

namespace teamwork\core\router;

use InvalidArgumentException;

class Route
{
    /** @var string Route name. */
    private string $name;

    /** @var array Route uri parts. */
    private array $uriParts = [];

    /** @var string Target controller. */
    private string $targetController;

    /** @var string Target action. */
    private string $targetAction;

    /**
     * Route constructor.
     * @param string $name Route name.
     * @param string $uriPath Route uri path.
     * @param string $targetController Target controller.
     * @param string $targetAction Target action.
     * @throws InvalidArgumentException uriPath contains invalid characters.
     */
    public function __construct(string $name, string $uriPath, string $targetController, string $targetAction)
    {
        $this->name = $name;
        $this->targetController = $targetController;
        $this->targetAction = $targetAction;

        $uriParts = explode('/', $uriPath);

        foreach ($uriParts as $uriPart) {
            if (substr($uriPart, 0, 1) === '{' && substr($uriPart, -1) === '}') {
                // Named Parameter {name}
                $parameterName = substr($uriPart, 1, -1);
                if (!preg_match('/^[A-Za-z0-9_]+$/', $parameterName)) throw new InvalidArgumentException('uriPath contains invalid characters.');

                $this->uriParts[] = [
                    'isNamedParameter' => true,
                    'value' => $parameterName
                ];
            } else {
                $this->uriParts[] = [
                    'isNamedParameter' => false,
                    'value' => $uriPart
                ];
            }
        }
    }

    /**
     * Check if the route matches the uri.
     * @param string $uri Uri to match.
     * @return bool true if the route matches the uri, else false.
     */
    public function isRouteMatch(string $uri): bool
    {
        $uriParts = explode('/', $uri);
        $uriPartsCount = count($uriParts);
        $routeUriPartsCount = count($this->uriParts);

        if ($uriPartsCount < $routeUriPartsCount - 1 || $uriPartsCount > $routeUriPartsCount) return false;

        for ($i = 0; $i < $uriPartsCount; $i++) {
            // Skip if it is a named parameter.
            if ($this->uriParts[$i]['isNamedParameter']) continue;

            // If the route did not match, most likely this is not the route.
            if ($this->uriParts[$i]['value'] !== $uriParts[$i]) return false;
        }

        return true;
    }

    /**
     * Get the route uri with parameters.
     * @param mixed ...$parameters Route uri parameters.
     * @return string route uri.
     */
    public function getRouteUri(...$parameters): string
    {
        $routeUriParts = [];
        $parametersCount = count($parameters);
        $index = 0;

        foreach ($this->uriParts as $uriPart) {
            if (!$uriPart['isNamedParameter']) {
                $routeUriParts[] = $uriPart['value'];
            } else {
                if ($index >= $parametersCount) break;
                $routeUriParts[] = $parameters[$index++];
            }
        }

        return implode('/', $routeUriParts);
    }

    /**
     * Get the route name.
     * @return string route name.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get the route uri parts.
     * @return array route uri parts.
     */
    public function getUriParts(): array
    {
        return $this->uriParts;
    }

    /**
     * Get the target controller for this route.
     * @return string target controller for this route.
     */
    public function getTargetController(): string
    {
        return $this->targetController;
    }

    /**
     * Get the target action for this route.
     * @return string target action for this route.
     */
    public function getTargetAction(): string
    {
        return $this->targetAction;
    }
}
