<?php declare(strict_types = 1);

/**
 * This file is part of the Simplex package.
 *
 * (c) Freddie Frantzen <freddie@freddiefrantzen.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Simplex\Routing;

class RouteParamsRegistry
{
    public const CONTROLLER_KEY = '_controller';

    public const ROUTE_KEY = '_route';

    private $routeParams;

    public function getRouteParams(): array
    {
        if (!isset($this->routeParams)) {
            throw new \LogicException('Route params have not been set');
        }

        return $this->routeParams;
    }

    public function setRouteParams(array $routeParams): void
    {
        $this->routeParams = $routeParams;
    }

    public function getParameter(string $key): string
    {
        if (!isset($this->routeParams[$key])) {
            throw new \RuntimeException("Route parameter '$key' does not exist'");
        }

        return $this->routeParams[$key];
    }
}
