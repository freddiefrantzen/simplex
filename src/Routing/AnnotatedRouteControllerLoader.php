<?php declare(strict_types = 1);

/**
 * This file is part of the Simplex package.
 *
 * (c) Freddie Frantzen <freddie@freddiefrantzen.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Simplex\Routing;

use Symfony\Component\Routing\Loader\AnnotationClassLoader;
use Symfony\Component\Routing\Route;

final class AnnotatedRouteControllerLoader extends AnnotationClassLoader
{
    /**
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function configureRoute(Route $route, \ReflectionClass $class, \ReflectionMethod $method, $annot)
    {
        $route->setDefault(
            RouteParamsRegistry::CONTROLLER_KEY,
            $class->getName() . '::' . $method->getName()
        );
    }
}
