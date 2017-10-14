<?php declare(strict_types=1);

namespace Simplex;

use DI\Container;
use DI\ContainerBuilder as PHPDIContainerBuilder;
use Simplex\DefinitionLoader\ChainDefinitionLoader;
use Simplex\DefinitionLoader\ConfigDefinitionLoader;
use Simplex\DefinitionLoader\CoreDefinitionLoader;
use Simplex\DefinitionLoader\ModuleDefinitionLoader;

class Bootstrap
{
    const CACHE_DIRECTORY_NAME = 'cache';
    const COMPILED_CONTAINER_DIRECTORY_NAME = 'container';

    /** @var Container */
    private static $container;

    public static function init(string $configDirectoryPath): void
    {
        $configDirectory = new \SplFileInfo($configDirectoryPath);

        $environmentVariableLoader = new EnvironmentVariableLoader();
        $environmentVariableLoader->load($configDirectory);

        $definitionLoader = new ChainDefinitionLoader(
            new CoreDefinitionLoader(),
            new ModuleDefinitionLoader($configDirectory),
            new ConfigDefinitionLoader($configDirectory, $environmentVariableLoader->getSimplexEnvironment())
        );

        $containerBuilder = new ContainerBuilder(
            $configDirectory,
            new PHPDIContainerBuilder(),
            $definitionLoader,
            $environmentVariableLoader->getSimplexEnvironment()
        );

        if ($environmentVariableLoader->getCompileContainer()) {

            $compiledContainerDirectory = self::getCompiledContainerDirectory($configDirectory);
            $containerBuilder->enableCompilation($compiledContainerDirectory);
        }

        self::$container = $containerBuilder->build();
    }

    private static function getCompiledContainerDirectory(\SplFileInfo $configDirectory): \SplFileInfo
    {
        return new \SplFileInfo(
            $configDirectory->getPathname()
            . DIRECTORY_SEPARATOR
            . self::CACHE_DIRECTORY_NAME
            . DIRECTORY_SEPARATOR
            . self::COMPILED_CONTAINER_DIRECTORY_NAME
        );
    }

    public static function getContainer(): Container
    {
        return self::$container;
    }
}
