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
use Doctrine\Common\Cache\ArrayCache;
use Doctrine\Common\Cache\FilesystemCache;
use Psr\Container\ContainerInterface;
use Simplex\Environment;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Routing\Loader\AnnotationDirectoryLoader;
use Symfony\Component\Routing\RouteCollection;

class AnnotationRouteCollectionBuilder implements RouteCollectionBuilder
{
    /** @var bool */
    private $enableCache;

    /** @var string */
    private $cacheDirectory;

    public function __construct(bool $enableCache, string $cacheDirectory)
    {
        $this->enableCache = $enableCache;
        $this->cacheDirectory = $cacheDirectory;
    }

    public function build(ContainerInterface $container, array $modules): RouteCollection
    {
        if (!$this->enableCache) {
            $cache = new ArrayCache();
        } else {
            $cache = new FilesystemCache($this->cacheDirectory . '/routing');
        }

        $reader = new CachedReader(
            new AnnotationReader(),
            $cache,
            $container->get(Environment::DEBUG_MODE_CONTAINER_KEY)
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
