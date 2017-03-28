<?php declare(strict_types=1);

/**
 * This file is part of the Simplex package.
 *
 * (c) Freddie Frantzen <freddie@freddiefrantzen.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require __DIR__ . '/stub/BaseController.php';
require __DIR__ . '/stub/Controller.php';

use Simplex\HttpMiddleware\DispatchController;
use Simplex\Routing\RouteParamsRegistry;
use Interop\Container\ContainerInterface;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
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
            '_route' => 'test',
            '_controller' => Controller::class . '::testAction',
            'id' => 'abc123',
            'foo' => 'bar'
        ];

        $registry = new RouteParamsRegistry();
        $registry->setRouteParams($routeParams);

        return new DispatchController($registry, $this->getStubbedContainer());
    }

    public function nextMiddleware()
    {
        return new class {
            public function __invoke(ServerRequestInterface $request, ResponseInterface $response) { return $response; }
        };
    }

    public function getStubbedContainer(): ContainerInterface
    {
        return new class implements ContainerInterface
        {
            public function get($id)
            {
                if ($id === 'controller_dependencies') {
                    return [
                        'baz' => new class() {
                            public function getName() {
                                return 'baz';
                            }
                        },
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
