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

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface;

class SetJsonResponseHeaders
{
    public function __invoke(ServerRequestInterface $request, Response $response, callable $next)
    {
        /** @var Response $response */
        $response = $next($request, $response);

        return $response
            ->withHeader('accept', 'application/json')
            ->withHeader('content-type', 'application/json');
    }
}
