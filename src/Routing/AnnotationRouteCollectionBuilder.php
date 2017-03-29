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
    public function build(ContainerInterface $container, array $modulePaths): RouteCollection
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
        foreach ($modulePaths as $index => $dir) {

            $controllerDir = $dir. '/Controller';
            if (!is_readable($controllerDir)) {
                continue;
            }

            $routeCollection->addCollection($annotationLoader->load($dir. '/Controller'));
        }

        return $routeCollection;
    }
}
