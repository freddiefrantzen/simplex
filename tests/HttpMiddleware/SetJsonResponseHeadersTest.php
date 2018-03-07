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
use Simplex\HttpMiddleware\SetJsonResponseHeaders;
use Zend\Diactoros\Response;
use Zend\Diactoros\ServerRequest;

final class SetJsonResponseHeadersTest extends TestCase
{
    public function test_it_sets_accept_header()
    {
        $middleware = new SetJsonResponseHeaders();

        /** @var Response $response */
        $response = $middleware(new ServerRequest(), new Response(), $this->nextMiddleware());

        self::assertTrue($response->hasHeader(SetJsonResponseHeaders::ACCEPT_HEADER_NAME));
        self::assertEquals(
            SetJsonResponseHeaders::ACCEPT_HEADER_VALUE,
            $response->getHeader(SetJsonResponseHeaders::ACCEPT_HEADER_NAME)[0]
        );
    }

    public function test_it_sets_content_type_header()
    {
        $middleware = new SetJsonResponseHeaders();

        /** @var Response $response */
        $response = $middleware(new ServerRequest(), new Response(), $this->nextMiddleware());

        self::assertTrue($response->hasHeader(SetJsonResponseHeaders::CONTENT_TYPE_HEADER_NAME));
        self::assertEquals(
            SetJsonResponseHeaders::CONTENT_TYPE_HEADER_VALUE,
            $response->getHeader(SetJsonResponseHeaders::CONTENT_TYPE_HEADER_NAME)[0]
        );
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
