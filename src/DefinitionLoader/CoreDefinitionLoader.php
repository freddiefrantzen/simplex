<?php declare(strict_types=1);

namespace Simplex\DefinitionLoader;

use DI\ContainerBuilder;

class CoreDefinitionLoader implements DefinitionLoader
{
    const DEFAULT_DEFINITION_FILE = __DIR__ . '/../config/services.php';

    /** @var array */
    private $configDefinitions = [];

    /** @var \SplFileInfo */
    private $servicesFile;

    public function __construct(array $configDefinitions = [], \SplFileInfo $servicesFile = null)
    {
        $this->configDefinitions = $configDefinitions;

        if (null === $servicesFile) {
            $this->servicesFile = new \SplFileInfo(self::DEFAULT_DEFINITION_FILE);
            return;
        }

        $this->servicesFile = $servicesFile;
    }

    public function load(ContainerBuilder $containerBuilder): void
    {
        if (count($this->configDefinitions) > 0) {
            $containerBuilder->addDefinitions($this->configDefinitions);
        }

        $containerBuilder->addDefinitions($this->servicesFile->getPathname());
    }
}
