<?php declare(strict_types=1);

/**
 * This file is part of the Simplex package.
 *
 * (c) Freddie Frantzen <freddie@freddiefrantzen.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Simplex;

use Interop\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Relay\RelayBuilder;

class HttpApplication
{
    /** @var Kernel */
    private $kernel;

    public function __construct(Kernel $kernel)
    {
        $this->kernel = $kernel;
    }

    public function handleRequest(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $container = $this->kernel->getContainer();
        $resolver = function($class) use ($container) {
            return $container->get($class);
        };

        $relayBuilder = new RelayBuilder($resolver);
        $relay = $relayBuilder->newInstance($this->getMiddleware($container));

        $response = $relay($request, $response);

        return $response;
    }

    private function getMiddleware(ContainerInterface $container): array
    {
        $queue = $container->get('middleware');

        return array_reverse($queue);
    }
}
