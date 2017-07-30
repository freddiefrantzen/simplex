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
use DI\ContainerBuilder as PHPDIContainerBuilder;
use Dotenv\Dotenv;
use Symfony\Component\Finder\Finder;

class ContainerBuilder
{
    const CONFIG_FILE_EXTENSION = '.php';

    const CONFIG_FILENAME = 'config.php';
    const MODULES_FILENAME = 'modules.php';
    const DOTENV_FILE_NAME = '.env';

    const FRAMEWORK_ENVIRONMENT_VARIABLE_NAME = 'FRAMEWORK_ENV';
    const DEFAULT_FRAMEWORK_ENVIRONMENT = 'dev';

    const CACHE_ENABLED_ENVIRONMENT_VARIABLE_NAME = 'CACHE_ENABLED';
    const CACHE_ENABLED_DEFAULT_VALUE = 0;

    const MODULES_CONFIG_KEY = 'modules';

    /**
     * @var \SplFileInfo
     */
    private $configDirectory;

    public function __construct(\SplFileInfo $configDirectory)
    {
        if (!$configDirectory->isDir() || !$configDirectory->isReadable()) {
            throw new \RuntimeException('Path to config directory is invalid ' . $configDirectory->getRealPath());
        }

        $this->configDirectory = $configDirectory;
    }

    public function build(): Container
    {
        $this->loadEnvironmentVars($this->configDirectory);

        $environment = getenv(self::FRAMEWORK_ENVIRONMENT_VARIABLE_NAME);
        $cacheEnabled = (bool) getenv(self::CACHE_ENABLED_ENVIRONMENT_VARIABLE_NAME);

        $compiledContainerClassName =  ucfirst($environment) . 'Container';
        $cacheDirectory = $this->configDirectory->getPath() . DIRECTORY_SEPARATOR . 'cache/container'; // @todo: remove from config

        $containerBuilder = new PHPDIContainerBuilder();

        if ($cacheEnabled) {
            $containerBuilder->enableCompilation($cacheDirectory, $compiledContainerClassName);
        }

        $compiledContainerClassFile = $cacheDirectory . '/' . $compiledContainerClassName . '.php';

        if ($cacheEnabled && file_exists($compiledContainerClassFile)) {
            // The container is already compiled
            return $containerBuilder->build();
        }

        $this->addDefinitions($containerBuilder);

        return $containerBuilder->build();
    }

    private function loadEnvironmentVars(\SplFileInfo $configDirectory): void
    {
        $dotEnvFile = new \SplFileInfo($configDirectory->getPath() . DIRECTORY_SEPARATOR . self::DOTENV_FILE_NAME);

        if ($dotEnvFile->isReadable()) {
            $dotenv = new Dotenv($dotEnvFile->getPath());
            $dotenv->load();
        }

        if (empty(getenv(self::FRAMEWORK_ENVIRONMENT_VARIABLE_NAME))) {
            putenv(self::FRAMEWORK_ENVIRONMENT_VARIABLE_NAME . '=' . self::DEFAULT_FRAMEWORK_ENVIRONMENT);
        }

        if (empty(getenv(self::CACHE_ENABLED_ENVIRONMENT_VARIABLE_NAME))) {
            putenv(self::CACHE_ENABLED_ENVIRONMENT_VARIABLE_NAME . '=' . self::CACHE_ENABLED_DEFAULT_VALUE);
        }
    }

    private function addDefinitions(PHPDIContainerBuilder $containerBuilder): void
    {
        //$this->addVendorDefinitions(); @todo

        $this->addCoreDefinitions($containerBuilder);

        $this->addModuleDefinitions($containerBuilder);

        $this->addBaseConfigDefinitions($containerBuilder);

        $this->addEnvironmentConfigDefinitions($containerBuilder);
    }

    private function addCoreDefinitions(PHPDIContainerBuilder $containerBuilder): void
    {
        $containerBuilder->addDefinitions(__DIR__ . '/config/services.php');
    }

    private function addModuleDefinitions(PHPDIContainerBuilder $containerBuilder): void
    {
        $moduleList = $this->getModuleList();

        foreach ($moduleList['modules']['app'] as $moduleClass) {

            $module = new $moduleClass();

            $containerBuilder->addDefinitions([
                get_class($module) => \DI\create(get_class($module)),
                'modules' => \DI\add([
                    \DI\get(get_class($module)),
                ]),
            ]);

            $this->loadModuleConfig($module, $containerBuilder);
        }
    }

    /** return Module[] */
    private function getModuleList(): array
    {
        $moduleFile = new \SplFileInfo(
            $this->configDirectory->getPathname() . DIRECTORY_SEPARATOR . self::MODULES_FILENAME
        );

        return $this->loadConfigArray($moduleFile);
    }

    private function loadConfigArray(\SplFileInfo $file): array
    {
        if (!$file->isFile() || !$file->isReadable()) {
            throw new \RuntimeException('Could not read config file ' . $file->getRealPath());
        }

        $config = include $file;

        if (!is_array($config)) {
            throw new \LogicException('Expected config file ' . $file->getPathname() . ' to return an array');
        }

        return $config;
    }

    private function loadModuleConfig(Module $module, PHPDIContainerBuilder $containerBuilder)
    {
        $configDirectoryPath = rtrim($module->getServiceConfigDirectory(), DIRECTORY_SEPARATOR);

        if (null === $configDirectoryPath) {
            return;
        }

        $configDirectory = $this->getConfigDirectory($configDirectoryPath);

        $finder = new Finder();
        $finder->files()->depth(0)->in($configDirectory->getPathname());

        foreach ($finder as $file) {
            $moduleConfig = $this->loadConfigArray($file);
            $containerBuilder->addDefinitions($moduleConfig);
        }
    }

    private function getConfigDirectory(string $configDirectoryPath): \SplFileInfo
    {
        $configDirectory = new \SplFileInfo(rtrim($configDirectoryPath, DIRECTORY_SEPARATOR));

        if (!$configDirectory->isDir() || !$configDirectory->isReadable()) {
            throw new \RuntimeException('Path to config directory is invalid ' . $configDirectory->getRealPath());
        }

        return $configDirectory;
    }

    private function addBaseConfigDefinitions(PHPDIContainerBuilder $containerBuilder): void
    {
        $baseConfigFile = new \SplFileInfo(
            $this->configDirectory->getPathname() . DIRECTORY_SEPARATOR . self::CONFIG_FILENAME
        );

        $containerBuilder->addDefinitions($baseConfigFile->getPathname());
    }

    private function addEnvironmentConfigDefinitions(PHPDIContainerBuilder $containerBuilder): void
    {
        $environment = getenv(self::FRAMEWORK_ENVIRONMENT_VARIABLE_NAME);
        $envConfigFilePath = $this->configDirectory->getPathname() . '/config_' . $environment . self::CONFIG_FILE_EXTENSION;

        $envConfigFile = new \SplFileInfo($envConfigFilePath);

        if (!$envConfigFile->isFile()) {
            return;
        }

        $containerBuilder->addDefinitions($envConfigFile->getPathname());
    }
}
