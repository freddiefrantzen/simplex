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
use Simplex\ContainerBuilder;
use Simplex\Tests\Stub\DefinitionLoader;
use Simplex\Tests\Stub\PHPDIContainerBuilder;
use Simplex\Tests\Util\VirtualFileSystemCapabilities;

class ContainerBuilderTest extends TestCase
{
    use VirtualFileSystemCapabilities;

    const CONFIG_DIRECTORY = 'config';
    const CACHE_DIRECTORY = 'cache';
    const COMPILED_CONTAINER_DIRECTORY = 'container';
    const COMPILED_CONTAINER_FILE = 'DevContainer.php';

    const ENVIRONMENT = 'dev';

    /** @var \SplFileInfo */
    private $configDirectory;

    /** @var PHPDIContainerBuilder */
    private $phpDiContainerBuilder;

    /** @var DefinitionLoader */
    private $definitionLoader;

    /** @var ContainerBuilder */
    private $containerBuilder;

    protected function setUp()
    {
        parent::setUp();

        $this->createVirtualFilesystem([
            self::CONFIG_DIRECTORY => [],
            self::CACHE_DIRECTORY => [],
        ]);

        $this->configDirectory = new \SplFileInfo($this->getVfsRoot() . DIRECTORY_SEPARATOR . self::CONFIG_DIRECTORY);
        $this->phpDiContainerBuilder = new PHPDIContainerBuilder();
        $this->definitionLoader = new DefinitionLoader();

        $this->containerBuilder = new ContainerBuilder(
            $this->configDirectory,
            $this->phpDiContainerBuilder,
            $this->definitionLoader,
            self::ENVIRONMENT
        );
    }

    protected function tearDown()
    {
        $this->destroyVirtualFilesystem();

        parent::tearDown();
    }

    public function test_definitions_loaded_if_compilation_not_enabled()
    {
        $this->containerBuilder->build();

        self::assertTrue($this->definitionLoader->wasCalled);
        self::assertTrue($this->phpDiContainerBuilder->containerBuilt);

        $this->phpDiContainerBuilder->reset();
        $this->definitionLoader->reset();

        $this->containerBuilder->build();

        self::assertTrue($this->definitionLoader->wasCalled);
        self::assertTrue($this->phpDiContainerBuilder->containerBuilt);
    }

    public function test_compiled_class_created_if_compilation_enabled_and_compiled_class_not_exists()
    {
        $this->enableCompilation();

        self::assertFileNotExists($this->getPathToCompiledContainerFile());

        $this->containerBuilder->build();

        self::assertFileExists($this->getPathToCompiledContainerFile());
    }

    private function enableCompilation(): void
    {
        $this->containerBuilder->enableCompilation(
            new \SplFileInfo(
                $this->getVfsRoot()
                . DIRECTORY_SEPARATOR
                . self::CACHE_DIRECTORY
                . DIRECTORY_SEPARATOR
                . self::COMPILED_CONTAINER_DIRECTORY
            )
        );
    }

    private function getPathToCompiledContainerFile(): string
    {
        return $this->getVfsRoot()
            . DIRECTORY_SEPARATOR
            . self::CACHE_DIRECTORY
            . DIRECTORY_SEPARATOR
            . self::COMPILED_CONTAINER_DIRECTORY
            . DIRECTORY_SEPARATOR
            . self::COMPILED_CONTAINER_FILE;
    }

    public function test_definitions_not_loaded_if_compilation_enabled_and_compiled_class_exists()
    {
        $this->enableCompilation();

        self::assertFileNotExists($this->getPathToCompiledContainerFile());

        $this->containerBuilder->build();

        self::assertFileExists($this->getPathToCompiledContainerFile());

        self::assertTrue($this->definitionLoader->wasCalled);
        self::assertTrue($this->phpDiContainerBuilder->containerBuilt);

        $this->phpDiContainerBuilder->reset();
        $this->definitionLoader->reset();

        $this->containerBuilder->build();

        self::assertFalse($this->definitionLoader->wasCalled);
        self::assertTrue($this->phpDiContainerBuilder->containerBuilt);
    }

    public function test_definitions_loaded_if_compilation_enabled_but_compiled_class_not_exists()
    {
        $this->enableCompilation();

        self::assertFileNotExists($this->getPathToCompiledContainerFile());

        $this->containerBuilder->build();

        self::assertTrue($this->definitionLoader->wasCalled);
        self::assertTrue($this->phpDiContainerBuilder->containerBuilt);
    }
}