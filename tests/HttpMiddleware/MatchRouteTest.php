<?php declare(strict_types = 1);

/**
 * This file is part of the Simplex package.
 *
 * (c) Freddie Frantzen <freddie@freddiefrantzen.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Simplex\Tests\HttpMiddleware;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Simplex\HttpMiddleware\MatchRoute;
use Simplex\Routing\RouteParamsRegistry;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Zend\Diactoros\Response;
use Zend\Diactoros\ServerRequest;

class MatchRouteTest extends TestCase
{
    const TEST_ROUTE_NAME = 'test';
    const TEST_ROUTE_URI = '/test';

    public function test_it_passes_the_matched_route_to_the_registry()
    {
        $routeCollection = new RouteCollection();
        $routeCollection->add(self::TEST_ROUTE_NAME, new Route(self::TEST_ROUTE_URI));

        $registry = new RouteParamsRegistry();

        $middleware = new MatchRoute($routeCollection, $registry);

        $request = new ServerRequest([], [], self::TEST_ROUTE_URI);
        $middleware($request, new Response(), $this->nextMiddleware());

        $params = $registry->getRouteParams();

        self::assertCount(1, $params);
        self::assertArrayHasKey(RouteParamsRegistry::ROUTE_KEY, $params);
        self::assertContains(self::TEST_ROUTE_NAME, $params);
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function nextMiddleware()
    {
        return new class {
            public function __invoke(ServerRequestInterface $request, ResponseInterface $response)
            {
                return $response;
            }
        };
    }
}
