<?php declare(strict_types=1);

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
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Simplex\ContainerKeys;
use Simplex\HttpMiddleware\DispatchController;
use Simplex\Routing\RouteParamsRegistry;
use Simplex\Tests\Stub\BaseController;
use Simplex\Tests\Stub\Controller;
use Zend\Diactoros\Response;
use Zend\Diactoros\ServerRequest;

class DispatchControllerTest extends TestCase
{
    protected function tearDown()
    {
        Controller::$wasCalledWthExpectedArgs = false;
        BaseController::$baz = null;

        parent::tearDown();
    }

    public function test_it_passes_args_in_correct_order()
    {
        $middleware = $this->getMiddleware();

        $middleware(new ServerRequest(), new Response(), $this->nextMiddleware());

        self::assertTrue(Controller::$wasCalledWthExpectedArgs);
    }

    public function test_it_injects_base_controller_dependencies()
    {
        $middleware = $this->getMiddleware();

        $middleware(new ServerRequest(), new Response(), $this->nextMiddleware());

        self::assertNotNull(BaseController::$baz);
    }

    private function getMiddleware()
    {
        $routeParams = [
            RouteParamsRegistry::ROUTE_KEY => 'test',
            RouteParamsRegistry::CONTROLLER_KEY => Controller::class . '::testAction',
            'id' => Controller::EXPECTED_VALUE_ID,
            'foo' => Controller::EXPECTED_VALUE_FOO
        ];

        $registry = new RouteParamsRegistry();
        $registry->setRouteParams($routeParams);

        return new DispatchController($registry, $this->getStubbedContainer());
    }

    public function nextMiddleware()
    {
        return new class {
            public function __invoke(ServerRequestInterface $request, ResponseInterface $response)
            {
                return $response;
            }
        };
    }

    public function getStubbedContainer(): ContainerInterface
    {
        return new class implements ContainerInterface
        {
            public function get($id)
            {
                if ($id === ContainerKeys::CONTROLLER_DEPENDENCIES) {
                    return [
                        Controller::class => [
                            'baz' => new class() {
                                public function getName()
                                {
                                    return 'baz';
                                }
                            },
                        ]
                    ];
                }

                if ($id === Controller::class) {
                    return new Controller();
                }
            }

            public function has($id)
            {
                return true;
            }
        };
    }
}
