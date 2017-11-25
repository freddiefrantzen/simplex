<?php declare(strict_types=1);

namespace Simplex;

use DI\Container;
use DI\ContainerBuilder as PHPDIContainerBuilder;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Simplex\DefinitionLoader\ChainDefinitionLoader;
use Simplex\DefinitionLoader\ConfigDefinitionLoader;
use Simplex\DefinitionLoader\CoreDefinitionLoader;
use Simplex\DefinitionLoader\ModuleDefinitionLoader;

class Bootstrap
{
    private const CONFIG_DIRECTORY_NAME = 'config';
    private const CACHE_DIRECTORY_NAME = 'cache';
    private const CONTAINER_DIRECTORY_NAME = 'container';

    /** @var Container */
    private $container;

    /** @var \SplFileInfo */
    private $projectRootDirectory;

    public function __construct(string $pathToProjectRoot)
    {
        $projectRootDirectory = new \SplFileInfo($pathToProjectRoot);

        if (!$projectRootDirectory->isDir() || !$projectRootDirectory->isReadable()) {
            throw new \RuntimeException('Invalid project root directory: ' . $projectRootDirectory->getRealPath());
        }

        $this->projectRootDirectory = new \SplFileInfo($pathToProjectRoot);
    }

    public function getContainer(): Container
    {
        if (null == $this->container) {
            $this->init();
        }

        return $this->container;
    }

    public function getProjectRootDirectory(): \SplFileInfo
    {
        return $this->projectRootDirectory;
    }

    public function getConfigDirectory(): \SplFileInfo
    {
        return new \SplFileInfo(
            $this->projectRootDirectory->getPathname() . DIRECTORY_SEPARATOR . self::CONFIG_DIRECTORY_NAME
        );
    }

    public function getCacheDirectory(): \SplFileInfo
    {
        return new \SplFileInfo(
            $this->projectRootDirectory->getPathname() . DIRECTORY_SEPARATOR . self::CACHE_DIRECTORY_NAME
        );
    }

    public function getCompiledContainerDirectory(): \SplFileInfo
    {
        return new \SplFileInfo(
            $this->getCacheDirectory()->getPathname() . DIRECTORY_SEPARATOR . self::CONTAINER_DIRECTORY_NAME
        );
    }

    private function init(): void
    {
        AnnotationRegistry::registerLoader('class_exists');

        (new Environment())->load($this->getProjectRootDirectory());

        $definitionLoader = $this->buildDefinitionLoader();

        $containerBuilder = $this->buildContainerBuilder($definitionLoader);

        $this->container = $containerBuilder->build();
    }

    private function buildDefinitionLoader(): ChainDefinitionLoader
    {
        $definitionLoader = new ChainDefinitionLoader(
            new CoreDefinitionLoader($this->buildCoreConfigDefinitions()),
            new ModuleDefinitionLoader($this->getConfigDirectory()),
            new ConfigDefinitionLoader($this->getConfigDirectory(), getenv(Environment::SIMPLEX_ENV))
        );
        return $definitionLoader;
    }

    private function buildCoreConfigDefinitions(): array
    {
        return [
            ContainerKeys::ENVIRONMENT => getenv(Environment::SIMPLEX_ENV),
            ContainerKeys::DEBUG_MODE => getenv(Environment::DEBUG_MODE),
            ContainerKeys::ENABLE_CACHE => getenv(Environment::ENABLE_CACHE),
            ContainerKeys::COMPILE_CONTAINER => getenv(Environment::COMPILE_CONTAINER),
            ContainerKeys::PROJECT_ROOT_DIRECTORY => $this->getProjectRootDirectory(),
            ContainerKeys::CONFIG_DIRECTORY => $this->getConfigDirectory(),
            ContainerKeys::CACHE_DIRECTORY => $this->getCacheDirectory(),
            ContainerKeys::COMPILED_CONTAINER_DIR => $this->getCompiledContainerDirectory(),
        ];
    }

    private function buildContainerBuilder($definitionLoader): ContainerBuilder
    {
        $containerBuilder = new ContainerBuilder(
            new PHPDIContainerBuilder(),
            $definitionLoader,
            getenv(Environment::SIMPLEX_ENV)
        );

        if (getenv(Environment::COMPILE_CONTAINER)) {
            $containerBuilder->enableCompilation($this->getCompiledContainerDirectory());
        }
        return $containerBuilder;
    }
}
