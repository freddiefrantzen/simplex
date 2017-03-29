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
use Simplex\HttpMiddleware\SetJsonResponseHeaders;
use Zend\Diactoros\Response;
use Zend\Diactoros\ServerRequest;

class SetJsonResponseHeadersTest extends TestCase
{
    public function test_it_sets_accept_header()
    {
        $middleware = new SetJsonResponseHeaders();

        /** @var Response $response */
        $response = $middleware(new ServerRequest(), new Response(), $this->nextMiddleware());

        self::assertTrue($response->hasHeader('accept'));
        self::assertEquals('application/json', $response->getHeader('accept')[0]);
    }

    public function test_it_sets_content_type_header()
    {
        $middleware = new SetJsonResponseHeaders();

        /** @var Response $response */
        $response = $middleware(new ServerRequest(), new Response(), $this->nextMiddleware());

        self::assertTrue($response->hasHeader('content-type'));
        self::assertEquals('application/json', $response->getHeader('content-type')[0]);
    }

    public function nextMiddleware()
    {
        return new class {
            public function __invoke(ServerRequestInterface $request, ResponseInterface $response) { return $response; }
        };
    }
}
