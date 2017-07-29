<?php declare(strict_types=1);

/**
 * This file is part of the Simplex package.
 *
 * (c) Freddie Frantzen <freddie@freddiefrantzen.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Simplex;

use DI\Container;
use DI\ContainerBuilder;
use Doctrine\Common\Cache\FilesystemCache;
use Psr\Container\ContainerInterface;
use Simplex\Routing\RouteCollectionBuilder;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\RequestContext;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;

class Kernel
{
    /** @var array */
    private $config;

    /** @var Container */
    private $container;

    /** @var bool */
    private $initialized = false;

    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    public function boot(): void
    {
        $exceptionHandler = $this->registerExceptionHandler();

        $containerBuilder = new ContainerBuilder();

        $this->loadContainerDefinitions($containerBuilder);

        $this->buildContainer($containerBuilder);

        if (null !== $exceptionHandler) {
            $exceptionHandler->setEditor($this->container->get('editor'));
        }

        $this->loadRoutes();

        $this->initialized = true;
    }

    private function registerExceptionHandler(): ?PrettyPageHandler
    {
        if (false == getenv('FRAMEWORK_DEBUG')) {
            return null;
        }

        $whoops = new Run;
        $handler = new PrettyPageHandler();
        $whoops->pushHandler($handler);
        $whoops->register();

        return $handler;
    }

    private function loadContainerDefinitions(ContainerBuilder $containerBuilder): void
    {
        $containerBuilder->addDefinitions(__DIR__ . '/config/services.php');

        $containerBuilder->addDefinitions($this->config);
    }

    private function loadRoutes(): void
    {
        $builder = $this->container->get(RouteCollectionBuilder::class);

        $routeCollection = $builder->build($this->container, $this->config['modules']);

        $urlGenerator = new UrlGenerator($routeCollection, new RequestContext());

        $this->container->set('route_collection', $routeCollection);
        $this->container->set(UrlGenerator::class, $urlGenerator);
    }

    private function buildContainer(ContainerBuilder $builder)
    {
        $this->configureContainerCache($builder);

        $this->container = $builder->build();
    }

    private function configureContainerCache(ContainerBuilder $builder): void
    {
        if (true == getenv('FRAMEWORK_DEBUG')) {
            return;
        }

        if (!isset($this->config['cache_dir'])) {
            return;
        }

        $cacheDir = rtrim($this->config['cache_dir'], '/');

        $builder->setDefinitionCache(new FilesystemCache($cacheDir . 'container'));
        $builder->writeProxiesToFile(true, 'tmp/proxies');
    }

    public function getContainer(): ContainerInterface
    {
        if (!$this->initialized) {
            throw new \RuntimeException('Kernel has not been initialized');
        }

        return $this->container;
    }
}
