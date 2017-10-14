<?php declare(strict_types=1);

/**
 * This file is part of the Simplex package.
 *
 * (c) Freddie Frantzen <freddie@freddiefrantzen.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use JMS\Serializer\SerializerInterface;
use Psr\Container\ContainerInterface;
use Simplex\Controller;
use Simplex\Environment;
use Simplex\HttpMiddleware\DispatchController;
use Simplex\HttpMiddleware\LoadRoutes;
use Simplex\HttpMiddleware\MatchRoute;
use Simplex\HttpMiddleware\RegisterExceptionHandler;
use Simplex\HttpMiddleware\SetJsonResponseHeaders;
use Simplex\Routing\AnnotationRouteCollectionBuilder;
use Simplex\Routing\RouteCollectionBuilder;
use Simplex\Routing\RouteParamsRegistry;
use Symfony\Component\Routing\Generator\UrlGenerator;

return [

    Environment::DEBUG_MODE_CONTAINER_KEY => DI\env(Environment::DEBUG_MODE_ENV_VAR, false),
    Environment::ENABLE_CACHE_CONTAINER_KEY => DI\env(Environment::ENABLE_CACHE_ENV_VAR, false),
    Environment::EDITOR_CONTAINER_KEY => DI\env(Environment::EDITOR_ENV_VAR, null),

    RegisterExceptionHandler::class => function (ContainerInterface $c) {
        return new RegisterExceptionHandler(
            (bool) $c->get(Environment::DEBUG_MODE_CONTAINER_KEY),
            (string) $c->get(Environment::EDITOR_CONTAINER_KEY)
        );
    },

    LoadRoutes::class => function (ContainerInterface $c) {
        return new LoadRoutes($c);
    },

    MatchRoute::class => function (ContainerInterface $c) {
        return new MatchRoute($c->get('route_collection'), $c->get(RouteParamsRegistry::class));
    },

    DispatchController::class => function (ContainerInterface $c) {
        return new DispatchController($c->get(RouteParamsRegistry::class), $c);
    },

    SetJsonResponseHeaders::class => function (ContainerInterface $c) {
        return new SetJsonResponseHeaders();
    },

    RouteParamsRegistry::class => function (ContainerInterface $c) {
        return new RouteParamsRegistry();
    },

    RouteCollectionBuilder::class => function (ContainerInterface $c) {
        return new AnnotationRouteCollectionBuilder(
            (bool) $c->get(Environment::ENABLE_CACHE_CONTAINER_KEY),
            CACHE_DIRECTORY
        );
    },

    'controller_dependencies' => DI\add(
        [
            Controller::class => [
                'urlGenerator' => DI\get(UrlGenerator::class),
                'serializer' => DI\get(SerializerInterface::class),
            ]
        ]
    ),

];
