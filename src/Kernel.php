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
use Dotenv\Dotenv;
use Interop\Container\ContainerInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\RequestContext;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;
use Simplex\Routing\RouteCollectionBuilder;

class Kernel
{
    private $baseDir;

    private $initialized = false;

    /** @var array */
    private $modulePaths = [];

    /** @var Container */
    private $container;

    public function __construct(string $baseDir)
    {
        $baseDir = rtrim($baseDir, '/');

        if (!file_exists($baseDir)) {
            throw new \RuntimeException("Base path does not exist: $baseDir");
        }

        $this->baseDir = $baseDir;
    }

    public function boot(): void
    {
        $this->loadEnvironmentVars();

        $exceptionHandler = $this->registerExceptionHandler();

        $containerBuilder = new ContainerBuilder();

        $this->loadConfig($containerBuilder);

        $this->loadModulePaths();

        $this->loadDefinitions($containerBuilder);

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

    private function loadEnvironmentVars(): void
    {
        if (is_readable($this->baseDir . '/.env')) {
            $dotenv = new Dotenv($this->baseDir);
            $dotenv->load();
        }

        if (empty(getenv('FRAMEWORK_ENV'))) {
            putenv('FRAMEWORK_ENV=dev');
        }
    }

    private function loadConfig(ContainerBuilder $containerBuilder): void
    {
        $containerBuilder->addDefinitions($this->baseDir . '/config/config.php');

        $envConfig = $this->baseDir . '/config/config_' . getenv('FRAMEWORK_ENV') . '.php';
        if (is_readable($envConfig)) {
            $containerBuilder->addDefinitions($envConfig);
        }
    }

    private function loadModulePaths(): void
    {
        $finder = new Finder();
        $finder->directories()->depth(0)->in($this->baseDir . '/src/Module');

        foreach ($finder as $dir) {

            $moduleDir = $dir->getPathname();

            $this->modulePaths[] = $moduleDir;
        }

        $moduleFile = $this->baseDir . '/config/modules.php';
        if (!is_readable($moduleFile)) {
            return;
        }

        $explicitlyDefinedModules = include $moduleFile;

        foreach ($explicitlyDefinedModules as $index => $dir) {
            if (!file_exists($dir)) {
                throw new \RuntimeException("Module path does not exist: $dir");
            }
        }

        $this->modulePaths = array_merge($this->modulePaths, $explicitlyDefinedModules);
    }

    private function loadDefinitions(ContainerBuilder $containerBuilder): void
    {
        $containerBuilder->addDefinitions($this->baseDir. '/src/Shared/config/services.php');

        foreach ($this->modulePaths as $index => $dir) {

            $configDir = $dir. '/config';
            if (!is_readable($configDir)) {
                continue;
            }

            $finder = new Finder();
            $finder->files()->depth(0)->in($configDir);

            foreach ($finder as $file) {
                $containerBuilder->addDefinitions($file->getPathname());
            }
        }
    }

    private function loadRoutes(): void
    {
        $builder = $this->container->get(RouteCollectionBuilder::class);

        $routeCollection = $builder->build($this->container, $this->modulePaths);

        $urlGenerator = new UrlGenerator($routeCollection, new RequestContext());

        $this->container->set('route_collection', $routeCollection);
        $this->container->set(UrlGenerator::class, $urlGenerator);
    }

    private function buildContainer(ContainerBuilder $builder)
    {
        if (false == getenv('FRAMEWORK_DEBUG')) {
            $builder->setDefinitionCache(new FilesystemCache($this->baseDir . '/cache/container'));
            $builder->writeProxiesToFile(true, 'tmp/proxies');
        }

        $this->container = $builder->build();
    }

    public function getContainer(): ContainerInterface
    {
        if (!$this->initialized) {
            throw new \RuntimeException('Kernel has not been initialized');
        }

        return $this->container;
    }
}
