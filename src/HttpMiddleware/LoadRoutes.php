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

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface;
use Simplex\ContainerKeys;
use Simplex\Routing\RouteCollectionBuilder;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\RequestContext;

class LoadRoutes
{
    /** @var ContainerInterface */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }
    
    public function __invoke(ServerRequestInterface $request, Response $response, callable $next)
    {
        $this->loadRoutes();

        $response = $next($request, $response);

        return $response;
    }

    private function loadRoutes(): void
    {
        $builder = $this->container->get(RouteCollectionBuilder::class);

        $modules = $this->container->get(ContainerKeys::MODULES);

        $routeCollection = $builder->build($this->container, $modules);

        $urlGenerator = new UrlGenerator($routeCollection, new RequestContext());

        $this->container->set(ContainerKeys::ROUTE_COLLECTION, $routeCollection);
        $this->container->set(UrlGenerator::class, $urlGenerator);
    }
}
