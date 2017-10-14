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
use Simplex\EnvironmentVariableLoader;
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

    'env' => DI\env(EnvironmentVariableLoader::DEFAULT_ENVIRONMENT),
    'compile_container' => DI\env(EnvironmentVariableLoader::COMPILE_CONTAINER_DEFAULT),

    'debug_mode' => DI\env('DEBUG_MODE', false),

    RegisterExceptionHandler::class => function (ContainerInterface $c) {
        return new RegisterExceptionHandler((bool) $c->get('debug_mode'), (string) $c->get('editor'));
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
        return new AnnotationRouteCollectionBuilder();
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
