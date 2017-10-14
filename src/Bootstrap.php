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
    /** @var Container */
    private static $container;

    public static function init(string $configDirectoryPath): void
    {
        $configDirectory = new \SplFileInfo($configDirectoryPath);

        $environment = new Environment();
        $environment->load($configDirectory);

        $definitionLoader = new ChainDefinitionLoader(
            new CoreDefinitionLoader(),
            new ModuleDefinitionLoader($configDirectory),
            new ConfigDefinitionLoader($configDirectory, $environment->getSimplexEnvironment())
        );

        $containerBuilder = new ContainerBuilder(
            $configDirectory,
            new PHPDIContainerBuilder(),
            $definitionLoader,
            $environment->getSimplexEnvironment()
        );

        if ($environment->getCompileContainer()) {

            $compiledContainerDirectory = self::getCompiledContainerDirectory();
            $containerBuilder->enableCompilation($compiledContainerDirectory);
        }

        self::$container = $containerBuilder->build();
    }

    private static function getCompiledContainerDirectory(): \SplFileInfo
    {
        return new \SplFileInfo(
            CACHE_DIRECTORY
            . DIRECTORY_SEPARATOR
            . ContainerBuilder::COMPILED_CONTAINER_DIRECTORY_NAME
        );
    }

    public static function getContainer(): Container
    {
        return self::$container;
    }
}
