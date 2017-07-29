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

use Dotenv\Dotenv;
use Symfony\Component\Finder\Finder;

class ConfigLoader
{
    const CONFIG_FILE_EXTENSION = '.php';

    const CONFIG_FILENAME = 'config.php';
    const MODULES_FILENAME = 'modules.php';
    const DOTENV_FILE_NAME = '.env';

    const FRAMEWORK_ENVIRONMENT_VARIABLE_NAME = 'FRAMEWORK_ENV';

    const DEFAULT_FRAMEWORK_ENVIRONMENT = 'dev';

    const MODULES_CONFIG_KEY = 'modules';

    public function loadFromDirectory(string $configDirectoryPath): array
    {
        $configDirectory = $this->getConfigDirectory($configDirectoryPath);

        $this->loadEnvironmentVars($configDirectory);

        $config = [];

        $this->loadModuleConfigs($configDirectory, $config);

        $this->loadBaseConfig($configDirectory, $config);

        $this->loadEnvConfig($configDirectory, $config);

        return $config;
    }

    private function getConfigDirectory(string $configDirectoryPath): \SplFileInfo
    {
        $configDirectory = new \SplFileInfo(rtrim($configDirectoryPath, DIRECTORY_SEPARATOR));

        if (!$configDirectory->isDir() || !$configDirectory->isReadable()) {
            throw new \RuntimeException('Path to config directory is invalid ' . $configDirectory->getRealPath());
        }

        return $configDirectory;
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
    }

    private function loadBaseConfig(\SplFileInfo $configDirectory, array &$config): void
    {
        $baseConfigFile = new \SplFileInfo(
            $configDirectory->getPathname() . DIRECTORY_SEPARATOR . self::CONFIG_FILENAME
        );

        $baseConfig = $this->loadConfigArray($baseConfigFile);

        $this->merge($config, $baseConfig);
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

    private function loadEnvConfig(\SplFileInfo $configDirectory, array &$config): void
    {
        $environment = getenv(self::FRAMEWORK_ENVIRONMENT_VARIABLE_NAME);
        $envConfigFilePath = $configDirectory->getPathname() . '/config_' . $environment . self::CONFIG_FILE_EXTENSION;

        $envConfigFile = new \SplFileInfo($envConfigFilePath);

        if (!$envConfigFile->isFile()) {
            return;
        }

        $envConfig = $this->loadConfigArray($envConfigFile);

        $this->merge($config, $envConfig);
    }

    private function merge(array &$config, array $configToMerge): void
    {
        foreach ($configToMerge as $key => $value) {
            if (!array_key_exists($key, $config)) {
                $config[$key] = $value;
                continue;
            }

            if (is_int($key)) {
                $config[] = $value;
            } elseif (is_array($value)) {
                $this->merge($config[$key], $value);
            } else {
                $config[$key] = $value;
            }
        }
    }

    private function loadModuleConfigs(\SplFileInfo $configDirectory, array &$config): void
    {
        $modules = $this->loadModules($configDirectory);

        $config[self::MODULES_CONFIG_KEY] = [];

        foreach ($modules as $module) {
            $this->addModuleToConfig($module, $config);
            $this->loadModuleConfig($module, $config);
        }
    }

    /** return Module[] */
    private function loadModules(\SplFileInfo $configDirectory): array
    {
        $moduleFile = new \SplFileInfo(
            $configDirectory->getPathname() . DIRECTORY_SEPARATOR . self::MODULES_FILENAME
        );

        return $this->loadConfigArray($moduleFile);
    }

    private function addModuleToConfig(Module $module, array &$config): void
    {
        $config[self::MODULES_CONFIG_KEY][get_class($module)] = $module;
    }

    private function loadModuleConfig(Module $module, array &$config)
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
            $this->merge($config, $moduleConfig);
        }
    }
}
