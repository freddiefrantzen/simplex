<?php declare(strict_types = 1);

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
use Simplex\DefinitionLoader\ConfigDefinitionLoader;
use Simplex\Tests\Util\VirtualFileSystemCapabilities;

final class ConfigDefinitionLoaderTest extends TestCase
{
    use VirtualFileSystemCapabilities;

    private const ENVIRONMENT = 'dev';

    private const CONFIG_DIR = 'config';
    private const ROOT_CONFIG_FILE = 'config.php';
    private const CONFIG_DEV_FILE = 'config_dev.php';

    private const CONFIG_PARAM_KEY = 'foo';
    private const CONFIG_PARAM_VALUE = 'bar';

    private const CONFIG_DEV_PARAM_KEY = 'buzz';
    private const CONFIG_DEV_PARAM_VALUE = 'baz';

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
        $configParamKey = self::CONFIG_PARAM_KEY;
        $configParamValue = self::CONFIG_PARAM_VALUE;

        $configDevParamKey = self::CONFIG_DEV_PARAM_KEY;
        $configDevParamValue = self::CONFIG_DEV_PARAM_VALUE;

        return [
            self::CONFIG_DIR => [
                self::ROOT_CONFIG_FILE => "<?php return ['$configParamKey' => '$configParamValue'];",
                self::CONFIG_DEV_FILE => "<?php return ['$configDevParamKey' => '$configDevParamValue'];",
            ]
        ];
    }

    public function test_root_config_definitions_loaded()
    {
        $this->createVirtualFilesystem($this->getBaseFileStructure());

        $container = $this->buildContainer();

        $this->assertTrue($container->has(self::CONFIG_PARAM_KEY));
        $this->assertEquals(self::CONFIG_PARAM_VALUE, $container->get(self::CONFIG_PARAM_KEY));
    }

    private function buildContainer(): Container
    {
        $definitionLoader = new ConfigDefinitionLoader(
            new \SplFileinfo($this->getVfsRoot() . DIRECTORY_SEPARATOR . self::CONFIG_DIR),
            self::ENVIRONMENT
        );

        $definitionLoader->load($this->containerBuilder);

        return $this->containerBuilder->build();
    }

    public function test_environment_config_definitions_loaded_when_environment_config_file_present()
    {
        $this->createVirtualFilesystem($this->getBaseFileStructure());

        $container = $this->buildContainer();

        $this->assertTrue($container->has(self::CONFIG_DEV_PARAM_KEY));
        $this->assertEquals(self::CONFIG_DEV_PARAM_VALUE, $container->get(self::CONFIG_DEV_PARAM_KEY));
    }

    public function test_environment_config_definitions_not_loaded_when_environment_config_file_not_present()
    {
        $fileStructure = $this->getBaseFileStructure();
        unset($fileStructure[self::CONFIG_DIR][self::CONFIG_DEV_FILE]);

        $this->createVirtualFilesystem($fileStructure);

        $container = $this->buildContainer();

        $this->assertFalse($container->has(self::CONFIG_DEV_PARAM_KEY));
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessageRegExp /\bInvalid path to config directory\b/
     */
    public function test_exception_thrown_if_path_to_root_config_directory_invalid()
    {
        new ConfigDefinitionLoader(
            new \SplFileInfo('invalid-path'),
            self::ENVIRONMENT
        );
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessageRegExp /\bConfig file not readable. Expected to find in\b/
     */
    public function test_exception_thrown_if_missing_root_config_file()
    {
        $fileStructure = $this->getBaseFileStructure();
        unset($fileStructure[self::CONFIG_DIR][self::ROOT_CONFIG_FILE]);

        $this->createVirtualFilesystem($fileStructure);

        $this->buildContainer();
    }
}
