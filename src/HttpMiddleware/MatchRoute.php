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
use Simplex\Routing\RouteParamsRegistry;
use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;

final class MatchRoute
{
    /** @var RouteCollection */
    private $routeCollection;

    /** @var RouteParamsRegistry */
    private $routeParamsRegistry;

    public function __construct(RouteCollection $routeCollection, RouteParamsRegistry $routeParamsRegistry)
    {
        $this->routeCollection = $routeCollection;
        $this->routeParamsRegistry = $routeParamsRegistry;
    }

    public function __invoke(ServerRequestInterface $request, Response $response, callable $next)
    {
        $routeParameters = $this->matchRoute($request);

        $this->routeParamsRegistry->setRouteParams($routeParameters);

        $response = $next($request, $response);

        return $response;
    }

    private function matchRoute(ServerRequestInterface $request): array
    {
        $factory = new HttpFoundationFactory();
        $symfonyRequest = $factory->createRequest($request);

        $context = new RequestContext();
        $context->fromRequest($symfonyRequest);

        $matcher = new UrlMatcher($this->routeCollection, $context);

        return $matcher->match($request->getUri()->getPath());
    }
}
