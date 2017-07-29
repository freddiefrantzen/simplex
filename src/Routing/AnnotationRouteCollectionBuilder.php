<?php declare(strict_types=1);

/**
 * This file is part of the Simplex package.
 *
 * (c) Freddie Frantzen <freddie@freddiefrantzen.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Simplex\Routing;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\CachedReader;
use Doctrine\Common\Cache\FilesystemCache;
use Psr\Container\ContainerInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Routing\Loader\AnnotationDirectoryLoader;
use Symfony\Component\Routing\RouteCollection;

class AnnotationRouteCollectionBuilder implements RouteCollectionBuilder
{
    public function build(ContainerInterface $container, array $modules): RouteCollection
    {
        $reader = new CachedReader(
            new AnnotationReader(),
            new FilesystemCache($container->get('cache_dir') . 'routing/'),
            $container->get('debug_mode')
        );

        $fileLoader = new FileLocator();
        $annotationClassLoader = new AnnotatedRouteControllerLoader($reader);
        $annotationLoader = new AnnotationDirectoryLoader($fileLoader, $annotationClassLoader);

        $routeCollection = new RouteCollection();

        foreach ($modules as $module) {
            $reflector = new \ReflectionClass(get_class($module));
            $fileInfo = new \SplFileInfo($reflector->getFileName());

            $controllerDir = $fileInfo->getPath() . '/Controller';

            if (!is_readable($controllerDir)) {
                continue;
            }

            $routeCollection->addCollection($annotationLoader->load($controllerDir));
        }

        return $routeCollection;
    }
}
