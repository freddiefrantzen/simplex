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
use Doctrine\Common\Cache\CacheProvider;
use Doctrine\Common\Cache\FilesystemCache;
use Psr\Container\ContainerInterface;
use Simplex\ContainerKeys;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Routing\Loader\AnnotationDirectoryLoader;
use Symfony\Component\Routing\RouteCollection;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class AnnotationRouteCollectionBuilder implements RouteCollectionBuilder
{
    const ROUTING_CACHE_DIRECTORY = 'router';

    const CONTROLLER_DIRECTORY = 'Controller';

    /** @var bool */
    private $enableCache;

    /** @var \SplFileInfo */
    private $cacheDirectory;

    public function __construct(bool $enableCache, \SplFileInfo $cacheDirectory)
    {
        $this->enableCache = $enableCache;
        $this->cacheDirectory = $cacheDirectory;
    }

    /**
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function build(ContainerInterface $container, array $modules): RouteCollection
    {
        $cache = $this->buildCache();

        $reader = new CachedReader(
            new AnnotationReader(),
            $cache,
            $container->get(ContainerKeys::DEBUG_MODE)
        );

        $fileLoader = new FileLocator();
        $annotationClassLoader = new AnnotatedRouteControllerLoader($reader);
        $annotationLoader = new AnnotationDirectoryLoader($fileLoader, $annotationClassLoader);

        $routeCollection = new RouteCollection();

        foreach ($modules as $module) {
            $reflector = new \ReflectionClass(get_class($module));
            $fileInfo = new \SplFileInfo($reflector->getFileName());

            $controllerDir = $fileInfo->getPath() . DIRECTORY_SEPARATOR . self::CONTROLLER_DIRECTORY;

            if (!is_readable($controllerDir)) {
                continue;
            }

            $routeCollection->addCollection($annotationLoader->load($controllerDir));
        }

        return $routeCollection;
    }

    private function buildCache(): CacheProvider
    {
        if (!$this->enableCache) {
            return new ArrayCache();
        }

        return new FilesystemCache(
            $this->cacheDirectory->getPathname() . DIRECTORY_SEPARATOR . self::ROUTING_CACHE_DIRECTORY
        );
    }
}
