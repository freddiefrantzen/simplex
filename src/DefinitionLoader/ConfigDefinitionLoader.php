<?php declare(strict_types=1);

namespace Simplex\DefinitionLoader;

use DI\ContainerBuilder;

class ConfigDefinitionLoader implements DefinitionLoader
{
    const CONFIG_FILENAME = 'config';
    const CONFIG_FILE_EXTENSION = '.php';

    /** @var \SplFileInfo */
    private $configDirectory;

    /** @var string */
    private $environment;

    public function __construct(\SplFileInfo $configDirectory, string $environment)
    {
        if (!$configDirectory->isDir()) {
            throw new \LogicException("Invalid path to config directory " . $configDirectory->getRealPath());
        }

        $this->configDirectory = $configDirectory;
        $this->environment = $environment;
    }

    public function load(ContainerBuilder $containerBuilder): void
    {
        $this->loadRootConfigDefinitions($containerBuilder);
        $this->loadEnvironmentConfigDefinitions($containerBuilder);
    }

    private function loadRootConfigDefinitions(ContainerBuilder $containerBuilder): void
    {
        $rootConfigFile = new \SplFileInfo(
            $this->configDirectory->getPathname()
            . DIRECTORY_SEPARATOR
            . self::CONFIG_FILENAME
            . self::CONFIG_FILE_EXTENSION
        );

        if (!$rootConfigFile->isReadable()) {
            throw new \LogicException(
                "Config file not readable. Expected to find in " . $this->configDirectory->getRealPath()
            );
        }

        $containerBuilder->addDefinitions($rootConfigFile->getPathname());
    }

    private function loadEnvironmentConfigDefinitions(ContainerBuilder $containerBuilder): void
    {
        $envConfigFilePath = $this->configDirectory->getPathname()
            . DIRECTORY_SEPARATOR
            . self::CONFIG_FILENAME
            . '_'
            . $this->environment
            . self::CONFIG_FILE_EXTENSION;

        $envConfigFile = new \SplFileInfo($envConfigFilePath);

        if (!$envConfigFile->isFile()) {
            return;
        }

        $containerBuilder->addDefinitions($envConfigFile->getPathname());
    }
}
