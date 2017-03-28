<?php declare(strict_types=1);

/**
 * This file is part of the Simplex package.
 *
 * (c) Freddie Frantzen <freddie@freddiefrantzen.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Interop\Container\ContainerInterface;
use Simplex\HttpMiddleware\DispatchController;
use Simplex\Routing\RouteParamsRegistry;
use Simplex\HttpMiddleware\MatchRoute;
use Simplex\Routing\RouteCollectionBuilder;
use Simplex\Routing\AnnotationRouteCollectionBuilder;
use Symfony\Component\Routing\Generator\UrlGenerator;
use JMS\Serializer\SerializerInterface;
use Simplex\HttpMiddleware\SetJsonResponseHeaders;

return [

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

    'controller_dependencies' => DI\add([
        'urlGenerator' => DI\get(UrlGenerator::class),
        'serializer' => DI\get(SerializerInterface::class),
    ]),

];
