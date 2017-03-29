<?php declare(strict_types=1);

/**
 * This file is part of the Simplex package.
 *
 * (c) Freddie Frantzen <freddie@freddiefrantzen.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Simplex\HttpMiddleware\MatchRoute;
use Simplex\Routing\RouteParamsRegistry;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Zend\Diactoros\Response;
use Zend\Diactoros\ServerRequest;

class MAtchRouteTest extends TestCase
{
    public function test_it_passes_the_matched_route_to_the_registry()
    {
        $routeCollection = new RouteCollection();
        $routeCollection->add('test', new Route('/test'));

        $registry = new RouteParamsRegistry();

        $middleware = new MatchRoute($routeCollection, $registry);

        $request = new ServerRequest([], [], '/test');
        $middleware($request, new Response(), $this->nextMiddleware());

        $params = $registry->getRouteParams();

        self::assertCount(1, $params);
        self::assertArrayHasKey('_route', $params);
        self::assertContains('test', $params);
    }

    public function nextMiddleware()
    {
        return new class {
            public function __invoke(ServerRequestInterface $request, ResponseInterface $response) { return $response; }
        };
    }
}
