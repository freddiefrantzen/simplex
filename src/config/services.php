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
use Simplex\ContainerKeys;
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

    ContainerKeys::DEBUG_MODE => DI\env(Environment::DEBUG_MODE_ENV_VAR, false),
    ContainerKeys::ENABLE_CACHE => DI\env(Environment::ENABLE_CACHE_ENV_VAR, false),
    ContainerKeys::EDITOR => DI\env(Environment::EDITOR_ENV_VAR, null),

    RegisterExceptionHandler::class => function (ContainerInterface $c) {
        return new RegisterExceptionHandler(
            (bool) $c->get(ContainerKeys::DEBUG_MODE),
            (string) $c->get(ContainerKeys::EDITOR)
        );
    },

    LoadRoutes::class => function (ContainerInterface $c) {
        return new LoadRoutes($c);
    },

    MatchRoute::class => function (ContainerInterface $c) {
        return new MatchRoute($c->get(ContainerKeys::ROUTE_COLLECTION), $c->get(RouteParamsRegistry::class));
    },

    DispatchController::class => function (ContainerInterface $c) {
        return new DispatchController($c->get(RouteParamsRegistry::class), $c);
    },

    SetJsonResponseHeaders::class => function () {
        return new SetJsonResponseHeaders();
    },

    RouteParamsRegistry::class => function () {
        return new RouteParamsRegistry();
    },

    RouteCollectionBuilder::class => function (ContainerInterface $c) {
        return new AnnotationRouteCollectionBuilder(
            (bool) $c->get(ContainerKeys::ENABLE_CACHE),
            CACHE_DIRECTORY
        );
    },

    ContainerKeys::CONTROLLER_DEPENDENCIES => DI\add(
        [
            Controller::class => [
                'urlGenerator' => DI\get(UrlGenerator::class),
                'serializer' => DI\get(SerializerInterface::class),
            ]
        ]
    ),

];
