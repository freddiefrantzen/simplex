<?php declare(strict_types = 1);

/**
 * This file is part of the Simplex package.
 *
 * (c) Freddie Frantzen <freddie@freddiefrantzen.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Simplex\Tests;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Simplex\ContainerKeys;
use Simplex\HttpApplication;
use Zend\Diactoros\Response;
use Zend\Diactoros\ServerRequest;

final class HttpApplicationTest extends TestCase
{
    public const MIDDLEWARE_STUB_CONTAINER_KEY = 'test_middleware';

    public function test_it_returns_a_response()
    {
        $application = new HttpApplication($this->getStubbedContainer());

        $response = $application->handleRequest(new ServerRequest(), new Response());

        self::assertInstanceOf(Response::class, $response);
    }

    public function test_it_invokes_middleware()
    {
        $container = $this->getStubbedContainer();

        $application = new HttpApplication($container);

        $application->handleRequest(new ServerRequest(), new Response());

        $middleware = $container->get(self::MIDDLEWARE_STUB_CONTAINER_KEY);

        self::assertTrue($middleware::$wasInvoked);
    }

    /**
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function getStubbedContainer(): ContainerInterface
    {
        return new class implements ContainerInterface
        {
            public function get($id)
            {
                if ($id === ContainerKeys::MIDDLEWARE) {
                    return [
                        HttpApplicationTest::MIDDLEWARE_STUB_CONTAINER_KEY
                    ];
                }

                if ($id === HttpApplicationTest::MIDDLEWARE_STUB_CONTAINER_KEY) {
                    return new class
                    {
                        public static $wasInvoked = false;

                        public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next)
                        {
                            $response = $next($request, $response);
                            self::$wasInvoked = true;
                            return $response;
                        }
                    };
                }
            }

            public function has($id)
            {
                return true;
            }
        };
    }
}
