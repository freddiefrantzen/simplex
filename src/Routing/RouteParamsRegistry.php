<?php declare(strict_types=1);

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
    private $routeParams;

    public function getRouteParams(): array
    {
        if (!isset($this->routeParams)) {
            throw new \LogicException('Route params have not been set');
        }

        return $this->routeParams;
    }

    public function setRouteParams(array $routeParams)
    {
        $this->routeParams = $routeParams;
    }
}
