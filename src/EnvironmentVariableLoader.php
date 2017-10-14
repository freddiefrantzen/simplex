<?php declare(strict_types=1);

namespace Simplex;

use Dotenv\Dotenv;

class EnvironmentVariableLoader
{
    const DOTENV_FILE_NAME = '.env';

    const ENVIRONMENT_KEY = 'SIMPLEX_ENV';
    const COMPILE_CONTAINER_KEY = 'COMPILE_CONTAINER';

    const DEFAULT_ENVIRONMENT = 'dev';
    const COMPILE_CONTAINER_DEFAULT = '0';

    const REQUIRED_VARIABLE_DEFAULTS = [
        self::ENVIRONMENT_KEY => self::DEFAULT_ENVIRONMENT,
        self::COMPILE_CONTAINER_KEY => self::COMPILE_CONTAINER_DEFAULT,
    ];

    /** @var bool */
    private $loaded = false;

    public function load(\SplFileInfo $configDirectory): void
    {
        $dotEnvFile = $this->getDotEnvFile($configDirectory);

        $this->loadDotEnvParameters($dotEnvFile);

        $this->ensureRequireVariablesLoaded();

        $this->loaded = true;
    }

    private function getDotEnvFile(\SplFileInfo $configDirectory): \SplFileInfo
    {
        return new \SplFileInfo(
            $configDirectory->getPathname()
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

    public function getSimplexEnvironment(): string
    {
        if (!$this->loaded) {
            throw new \LogicException('Environment variables not loaded');
        }

        return getenv(self::ENVIRONMENT_KEY);
    }

    public function getCompileContainer(): bool
    {
        if (!$this->loaded) {
            throw new \LogicException('Environment variables not loaded');
        }

        return (bool) getenv(self::COMPILE_CONTAINER_KEY);
    }
}
