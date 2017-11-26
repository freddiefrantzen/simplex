<?php declare(strict_types = 1);

namespace Simplex;

use Dotenv\Dotenv;

class Environment
{
    public const SIMPLEX_ENV = 'SIMPLEX_ENV';
    public const DEBUG_MODE = 'DEBUG_MODE';
    public const ENABLE_CACHE = 'ENABLE_CACHE';
    public const COMPILE_CONTAINER = 'COMPILE_CONTAINER';

    private const DOTENV_FILE_NAME = '.env';

    public function load(\SplFileInfo $projectRootDirectory): void
    {
        $dotEnvFile = $this->getDotEnvFile($projectRootDirectory);

        $this->loadDotEnvParameters($dotEnvFile);
    }

    private function getDotEnvFile(\SplFileInfo $projectRootDirectory): \SplFileInfo
    {
        return new \SplFileInfo(
            $projectRootDirectory->getPathname() . DIRECTORY_SEPARATOR . self::DOTENV_FILE_NAME
        );
    }

    private function loadDotEnvParameters(\SplFileInfo $dotEnvFile): void
    {
        $dotenv = new Dotenv($dotEnvFile->getPath());

        if ($dotEnvFile->isReadable()) {
            $dotenv->load();
        }

        $dotenv->required(self::SIMPLEX_ENV)->notEmpty();
        $dotenv->required(self::ENABLE_CACHE)->isInteger();
        $dotenv->required(self::DEBUG_MODE)->isInteger();
        $dotenv->required(self::COMPILE_CONTAINER)->isInteger();
    }
}
