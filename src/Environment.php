<?php declare(strict_types=1);

namespace Simplex;

use Dotenv\Dotenv;

class Environment
{
    const SIMPLEX_ENVIRONMENT_ENV_VAR = 'SIMPLEX_ENV';
    const COMPILE_CONTAINER_ENV_VAR = 'COMPILE_CONTAINER';

    const ENABLE_CACHE_ENV_VAR = 'ENABLE_CACHE';
    const ENABLE_CACHE_CONTAINER_KEY = 'enable_cache';

    const DEBUG_MODE_ENV_VAR = 'DEBUG_MODE';
    const DEBUG_MODE_CONTAINER_KEY = 'debug_mode';

    const EDITOR_ENV_VAR = 'EDITOR';
    const EDITOR_CONTAINER_KEY = 'editor';

    const DOTENV_FILE_NAME = '.env';

    const DEFAULT_ENVIRONMENT = 'dev';
    const COMPILE_CONTAINER_DEFAULT = '0';
    const DEFAULT_CACHE_DIRECTORY = 'cache';

    const REQUIRED_VARIABLE_DEFAULTS = [
        self::SIMPLEX_ENVIRONMENT_ENV_VAR => self::DEFAULT_ENVIRONMENT,
        self::COMPILE_CONTAINER_ENV_VAR => self::COMPILE_CONTAINER_DEFAULT,
    ];

    /** @var \SplFileInfo */
    private $configDirectory;

    /** @var bool */
    private $loaded = false;

    public function load(\SplFileInfo $configDirectory): void
    {
        $this->configDirectory = $configDirectory;

        $dotEnvFile = $this->getDotEnvFile();

        $this->loadDotEnvParameters($dotEnvFile);

        $this->ensureRequireVariablesLoaded();

        if (!defined('CACHE_DIRECTORY')) {
            define('CACHE_DIRECTORY', $this->getDefaultCacheDirectory());
        }

        $this->loaded = true;
    }

    private function getDotEnvFile(): \SplFileInfo
    {
        return new \SplFileInfo(
            $this->configDirectory->getPathname()
            . DIRECTORY_SEPARATOR
            . '..'
            . DIRECTORY_SEPARATOR
            . self::DOTENV_FILE_NAME
        );
    }

    private function loadDotEnvParameters(\SplFileInfo $dotEnvFile): void
    {
        if ($dotEnvFile->isReadable()) {
            $dotenv = new Dotenv($dotEnvFile->getPath());
            $dotenv->load();
        }
    }

    private function ensureRequireVariablesLoaded(): void
    {
        foreach (self::REQUIRED_VARIABLE_DEFAULTS as $variableName => $defaultValue) {
            if (empty(getenv($variableName))) {
                putenv($variableName . '=' . $defaultValue);
            }
        }
    }

    private function getDefaultCacheDirectory(): string
    {
        return $this->configDirectory->getPath()
            . DIRECTORY_SEPARATOR
            . self::DEFAULT_CACHE_DIRECTORY;
    }

    public function getSimplexEnvironment(): string
    {
        if (!$this->loaded) {
            throw new \LogicException('Environment variables not loaded');
        }

        return getenv(self::SIMPLEX_ENVIRONMENT_ENV_VAR);
    }

    public function getCompileContainer(): bool
    {
        if (!$this->loaded) {
            throw new \LogicException('Environment variables not loaded');
        }

        return (bool) getenv(self::COMPILE_CONTAINER_ENV_VAR);
    }
}
