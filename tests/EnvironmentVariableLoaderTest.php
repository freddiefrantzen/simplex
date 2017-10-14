<?php declare(strict_types=1);

/**
 * This file is part of the Simplex package.
 *
 * (c) Freddie Frantzen <freddie@freddiefrantzen.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Simplex\Tests;

use PHPUnit\Framework\TestCase;

use Simplex\Environment;
use Simplex\Tests\Util\VirtualFileSystemCapabilities;

class EnvironmentVariableLoaderTest extends TestCase
{
    use VirtualFileSystemCapabilities;

    const DOTENV_FILENAME = '.env';

    const CONFIG_DIR_NAME = 'config';

    const FOO_ENV_VAR_NAME = 'FOO';
    const FOO_ENV_VAR_VALUE = 'bar';

    /** @var Environment */
    private $environmentVariableLoader;

    protected function setUp()
    {
        parent::setUp();

        $this->environmentVariableLoader = new Environment();
    }

    protected function tearDown()
    {
        $this->destroyVirtualFilesystem();

        parent::tearDown();
    }

    private function getBaseFileStructure(): array
    {
        return [
            self::CONFIG_DIR_NAME => [],
            self::DOTENV_FILENAME => self::FOO_ENV_VAR_NAME . '=' . self::FOO_ENV_VAR_VALUE
        ];
    }

    public function test_environment_variables_from_dot_env_file_loaded()
    {
        $this->createVirtualFilesystem($this->getBaseFileStructure());

        $this->environmentVariableLoader->load(
            new \SplFileInfo($this->getVfsRoot()
                . DIRECTORY_SEPARATOR
                . self::CONFIG_DIR_NAME
            )
        );

        self::assertEquals(self::FOO_ENV_VAR_VALUE, getenv(self::FOO_ENV_VAR_NAME));
    }

    public function test_defaults_set_if_not_present_in_dot_env_file()
    {
        $fileStructure = $this->getBaseFileStructure();
        unset($fileStructure[self::DOTENV_FILENAME]);

        $this->createVirtualFilesystem($fileStructure);

        $this->environmentVariableLoader->load(
            new \SplFileInfo($this->getVfsRoot()
                . DIRECTORY_SEPARATOR
                . self::CONFIG_DIR_NAME
            )
        );

        self::assertEquals(
            Environment::COMPILE_CONTAINER_DEFAULT,
            getenv(Environment::COMPILE_CONTAINER_ENV_VAR)
        );

        self::assertEquals(
            (bool) Environment::COMPILE_CONTAINER_DEFAULT,
            $this->environmentVariableLoader->getCompileContainer()
        );
    }
}
