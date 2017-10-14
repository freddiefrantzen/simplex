<?php declare(strict_types=1);

namespace Simplex\DefinitionLoader;

use DI\ContainerBuilder;

class CoreDefinitionLoader implements DefinitionLoader
{
    const DEFAULT_DEFINITION_FILE = __DIR__ . '/../config/services.php';

    /** @var \SplFileInfo */
    private $coreDefinitionsFile;

    public function __construct(\SplFileInfo $coreDefinitionsFile = null)
    {
        if (null === $coreDefinitionsFile) {
            $this->coreDefinitionsFile = new \SplFileInfo(self::DEFAULT_DEFINITION_FILE);
            return;
        }

        $this->coreDefinitionsFile = $coreDefinitionsFile;
    }

    public function load(ContainerBuilder $containerBuilder): void
    {
        $containerBuilder->addDefinitions($this->coreDefinitionsFile->getPathname());
    }
}
