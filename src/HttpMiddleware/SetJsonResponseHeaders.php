<?php declare(strict_types = 1);

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

final class SetJsonResponseHeaders
{
    public const ACCEPT_HEADER_NAME = 'accept';
    public const ACCEPT_HEADER_VALUE = 'application/json';

    public const CONTENT_TYPE_HEADER_NAME = 'content-type';
    public const CONTENT_TYPE_HEADER_VALUE = 'application/json';

    public function __invoke(ServerRequestInterface $request, Response $response, callable $next)
    {
        /** @var Response $response */
        $response = $next($request, $response);

        return $response
            ->withHeader(self::ACCEPT_HEADER_NAME, self::ACCEPT_HEADER_VALUE)
            ->withHeader(self::CONTENT_TYPE_HEADER_NAME, self::CONTENT_TYPE_HEADER_VALUE);
    }
}
