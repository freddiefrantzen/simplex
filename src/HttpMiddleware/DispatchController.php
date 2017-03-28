<?php declare(strict_types=1);

/**
 * This file is part of the Simplex package.
 *
 * (c) Freddie Frantzen <freddie@freddiefrantzen.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Simplex\HttpMiddleware;

use Simplex\Routing\RouteParamsRegistry;
use Interop\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface;

class DispatchController
{
    /** @var RouteParamsRegistry */
    private $routeParamsRegistry;

    /** @var ContainerInterface */
    private $container;

    public function __construct(RouteParamsRegistry $routeParamsRegistry, ContainerInterface $container)
    {
        $this->routeParamsRegistry = $routeParamsRegistry;
        $this->container = $container;
    }

    public function __invoke(ServerRequestInterface $request, Response $response, callable $next)
    {
        $response = $next($request, $response);

        $routeParameters = $this->routeParamsRegistry->getRouteParams();

        $controllerParts = explode('::', $routeParameters['_controller']);

        $controller = $this->getController($controllerParts[0]);

        $this->injectBaseControllerDependencies($controller);

        $args = $this->resolveArgs($request, $response, $controllerParts, $routeParameters);

        $response = call_user_func_array(
            [
                $controller,
                $controllerParts[1],
            ],
            $args
        );

        return $response;
    }

    private function getController(string $controllerClass)
    {
        return $this->container->get($controllerClass);
    }

    private function injectBaseControllerDependencies($controller)
    {
        $map = $this->container->get('controller_dependencies');

        foreach ($map as $setterName => $object) {
            $controller->{'set' . ucfirst($setterName)}($object);
        }
    }

    private function resolveArgs(
        ServerRequestInterface $request,
        Response $response,
        array $controllerParts,
        $routeParameters): array
    {
        $method = new \ReflectionMethod($controllerParts[0], $controllerParts[1]);

        $args = [
            $request,
            $response,
        ];

        $methodParameters = $method->getParameters();
        foreach ($methodParameters as $parameter) {
            if (array_key_exists($parameter->getName(), $routeParameters)) {
                $args[] = $routeParameters[$parameter->getName()];
            }
        }
        return $args;
    }
}
