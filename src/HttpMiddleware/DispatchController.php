<?php declare(strict_types = 1);

/**
 * This file is part of the Simplex package.
 *
 * (c) Freddie Frantzen <freddie@freddiefrantzen.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Simplex\HttpMiddleware;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Simplex\ContainerKeys;
use Simplex\Controller;
use Simplex\Routing\RouteParamsRegistry;

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

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next)
    {
        $controllerRouteParam = $this->routeParamsRegistry->getParameter(RouteParamsRegistry::CONTROLLER_KEY);

        $controller = $this->getController($this->getControllerClass($controllerRouteParam));

        $this->injectControllerDependencies($controller);

        $controller->setResponse($response);

        $args = $this->resolveArgs(
            $request,
            $this->getControllerClass($controllerRouteParam),
            $this->getControllerAction($controllerRouteParam)
        );

        $response = $this->invokeController(
            $controller,
            $this->getControllerAction($controllerRouteParam),
            $args
        );

        return $next($request, $response);
    }

    private function getControllerClass(string $controllerRouteParam): string
    {
        return explode('::', $controllerRouteParam)[0];
    }

    private function getControllerAction(string $controllerRouteParam): string
    {
        return explode('::', $controllerRouteParam)[1];
    }

    private function getController(string $controllerClass): Controller
    {
        return $this->container->get($controllerClass);
    }

    private function injectControllerDependencies($controller): void
    {
        if (!$this->container->has(ContainerKeys::CONTROLLER_DEPENDENCIES)) {
            return;
        }

        $map = $this->container->get(ContainerKeys::CONTROLLER_DEPENDENCIES);

        foreach ($map as $controllerClass => $dependencies) {
            if (!$controller instanceof $controllerClass) {
                continue;
            }

            foreach ($dependencies as $setterName => $object) {
                $controller->{'set' . ucfirst($setterName)}($object);
            }
        }
    }

    private function resolveArgs(
        ServerRequestInterface $request,
        string $controllerClass,
        string $controllerMethod
    ): array {
        $method = new \ReflectionMethod($controllerClass, $controllerMethod);

        $args = [];

        $methodParameters = $method->getParameters();

        $routeParameters = $this->routeParamsRegistry->getRouteParams();

        foreach ($methodParameters as $parameter) {

            if ($this->parameterIsOfType($parameter, ServerRequestInterface::class)) {
                $args[] = $request;
            }

            if (array_key_exists($parameter->getName(), $routeParameters)) {
                $args[] = $routeParameters[$parameter->getName()];
            }
        }

        return $args;
    }

    private function parameterIsOfType(\ReflectionParameter $parameter, string $class): bool
    {
        return ($parameter->getClass() !== null) && $parameter->getClass()->getName() === $class;
    }

    private function invokeController(Controller $controller, string $action, array $args): ResponseInterface
    {
        $response = call_user_func_array(
            [
                $controller,
                $action,
            ],
            $args
        );

        if (!$response instanceof ResponseInterface) {
            throw new \LogicException(
                get_class($controller) . ' must return an instance of ' . ResponseInterface::class
            );
        }

        return $response;
    }
}
