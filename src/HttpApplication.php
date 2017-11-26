<?php declare(strict_types = 1);

/**
 * This file is part of the Simplex package.
 *
 * (c) Freddie Frantzen <freddie@freddiefrantzen.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Simplex;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Relay\RelayBuilder;

class HttpApplication
{
    /** @var ContainerInterface */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function handleRequest(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $container = $this->container;

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
        return $container->get(ContainerKeys::MIDDLEWARE);
    }
}
