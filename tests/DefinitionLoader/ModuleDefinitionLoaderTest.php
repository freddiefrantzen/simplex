<?php declare(strict_types=1);

/**
 * This file is part of the Simplex package.
 *
 * (c) Freddie Frantzen <freddie@freddiefrantzen.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Simplex\Tests\DefinitionLoader;

use DI\Container;
use DI\ContainerBuilder;
use PHPUnit\Framework\TestCase;
use Simplex\DefinitionLoader\ModuleDefinitionLoader;
use Simplex\Tests\Stub\Module;
use Simplex\Tests\Util\VirtualFileSystemCapabilities;

class ModuleDefinitionLoaderTest extends TestCase
{
    use VirtualFileSystemCapabilities;

    const CONFIG_DIR = 'config';
    const SOURCES_DIR = 'src';

    const MODULES_FILENAME = 'modules.php';
    const MODULES_CONTAINER_KEY = 'modules';

    const MODULE_CLASS_NAME = Module::class;
    const MODULE_CLASS_FILENAME = self::MODULE_CLASS_NAME . '.php';

    const MODULE_DEFINITIONS_DIRECTORY_NAME = 'di';
    const MODULE_DEFINITIONS_FILENAME = 'definitions.php';

    const MODULE_PARAM_KEY = 'foo';
    const MODULE_PARAM_VALUE = 'bar';

    /** @var ContainerBuilder */
    private $containerBuilder;

    protected function setUp()
    {
        parent::setUp();

        $this->containerBuilder = new ContainerBuilder();
    }

    protected function tearDown()
    {
        $this->destroyVirtualFilesystem();

        parent::tearDown();
    }

    private function getBaseFileStructure(): array
    {
        $moduleParamKey = self::MODULE_PARAM_KEY;
        $moduleParamValue = self::MODULE_PARAM_VALUE;
        $moduleClassName = self::MODULE_CLASS_NAME;

        return [
            self::CONFIG_DIR => [
                self::MODULES_FILENAME => "<?php return [new $moduleClassName()];",
            ],
            self::MODULE_DEFINITIONS_DIRECTORY_NAME => [
                self::MODULE_DEFINITIONS_FILENAME => "<?php return ['$moduleParamKey' => '$moduleParamValue'];"
            ],
        ];
    }

    public function test_module_added_to_container()
    {
        $this->createVirtualFilesystem($this->getBaseFileStructure());

        $container = $this->buildContainer();

        self::assertTrue($container->has(self::MODULES_CONTAINER_KEY));

        $modules = $container->get(self::MODULES_CONTAINER_KEY);

        self::assertInternalType('array', $modules);
        self::assertInstanceOf(self::MODULE_CLASS_NAME, array_pop($modules));
    }

    private function buildContainer(): Container
    {
        $definitionLoader = new ModuleDefinitionLoader(
            new \SplFileInfo($this->getVfsRoot() . DIRECTORY_SEPARATOR . self::CONFIG_DIR)
        );

        $definitionLoader->load($this->containerBuilder);

        return $this->containerBuilder->build();
    }

    public function test_module_definitions_loaded()
    {
        $this->createVirtualFilesystem($this->getBaseFileStructure());

        Module::$serviceDefinitionDirectory = $this->getVfsRoot()
            . DIRECTORY_SEPARATOR
            . self::MODULE_DEFINITIONS_DIRECTORY_NAME;

        $container = $this->buildContainer();

        self::assertTrue($container->has(self::MODULE_PARAM_KEY));
        self::assertEquals(self::MODULE_PARAM_VALUE, $container->get(self::MODULE_PARAM_KEY));
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Invalid module definitions directory path
     */
    public function test_exception_thrown_if_module_definition_directory_path_invalid()
    {
        $this->createVirtualFilesystem($this->getBaseFileStructure());

        Module::$serviceDefinitionDirectory = 'invalid-path';

        $this->buildContainer();
    }

    public function test_module_definitions_not_loaded_if_definition_directory_not_specified()
    {
        $this->createVirtualFilesystem($this->getBaseFileStructure());

        Module::$serviceDefinitionDirectory = null;

        $container = $this->buildContainer();

        self::assertFalse($container->has(self::MODULE_PARAM_KEY));
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Could not read modules file
     */
    public function test_exception_thrown_if_module_file_missing()
    {
        $fileStructure = $this->getBaseFileStructure();
        unset($fileStructure[self::CONFIG_DIR][self::MODULES_FILENAME]);

        $this->createVirtualFilesystem($fileStructure);

        $this->buildContainer();
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessageRegExp /\bto return an array\b/
     */
    public function test_exception_thrown_if_module_file_does_not_return_an_array()
    {
        $fileStructure = $this->getBaseFileStructure();
        $fileStructure[self::CONFIG_DIR][self::MODULES_FILENAME] = "<?php ";

        $this->createVirtualFilesystem($fileStructure);

        $this->buildContainer();
    }
}
