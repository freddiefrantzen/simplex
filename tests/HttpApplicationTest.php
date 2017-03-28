<?php declare(strict_types=1);

/**
 * This file is part of the Simplex package.
 *
 * (c) Freddie Frantzen <freddie@freddiefrantzen.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Simplex\HttpApplication;
use Simplex\Kernel;
use Interop\Container\ContainerInterface;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response;
use Zend\Diactoros\ServerRequest;

class HttpApplicationTest extends TestCase
{
    public function test_it_returns_a_response()
    {
        $kernel = $this->getStubbedKernel();
        $application = new HttpApplication($kernel);

        $response = $application->handleRequest(new ServerRequest(), new Response());

        self::assertInstanceOf(Response::class, $response);
    }

    public function test_it_invokes_middleware()
    {
        $kernel = $this->getStubbedKernel();
        $application = new HttpApplication($kernel);

        $application->handleRequest(new ServerRequest(), new Response());

        $middleware = $kernel->getContainer()->get('test_middleware');

        self::assertTrue($middleware::$wasInvoked);
    }

    public function getStubbedKernel(): Kernel
    {
        return new class extends Kernel
        {
            public function __construct() {}

            public function getContainer(): ContainerInterface
            {
                return new class implements ContainerInterface
                {
                    public function get($id)
                    {
                        if ($id === 'middleware') {
                            return [
                                'test_middleware'
                            ];
                        }

                        if ($id === 'test_middleware') {
                            return new class {
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
        };
    }
}
