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

use PHPUnit\Framework\TestCase;

use DI\Container;
use DI\ContainerBuilder;
use Simplex\DefinitionLoader\CoreDefinitionLoader;
use Simplex\Tests\Util\VirtualFileSystemCapabilities;

class CoreDefinitionLoaderTest extends TestCase
{
    use VirtualFileSystemCapabilities;

    const CORE_DEFINITIONS_DIR = 'di';
    const CORE_DEFINITIONS_FILE = 'definitions.php';

    const CORE_PARAM_KEY = 'foo';
    const CORE_PARAM_VALUE = 'bar';

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
        $coreParamKey = self::CORE_PARAM_KEY;
        $coreParamValue = self::CORE_PARAM_VALUE;

        return [
            self::CORE_DEFINITIONS_DIR => [
                self::CORE_DEFINITIONS_FILE => "<?php return ['$coreParamKey' => '$coreParamValue'];",
            ],
        ];
    }

    public function test_core_definitions_loaded()
    {
        $this->createVirtualFilesystem($this->getBaseFileStructure());

        $container = $this->buildContainer();

        self::assertTrue($container->has(self::CORE_PARAM_KEY));
        self::assertEquals(self::CORE_PARAM_VALUE, $container->get(self::CORE_PARAM_KEY));
    }

    private function buildContainer(): Container
    {
        $definitionLoader = new CoreDefinitionLoader(
            new \SplFileInfo($this->getVfsRoot()
                . DIRECTORY_SEPARATOR
                . self::CORE_DEFINITIONS_DIR
                . DIRECTORY_SEPARATOR
                . self::CORE_DEFINITIONS_FILE
            )
        );

        $definitionLoader->load($this->containerBuilder);

        return $this->containerBuilder->build();
    }
}
