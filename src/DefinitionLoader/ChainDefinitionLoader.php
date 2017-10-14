<?php declare(strict_types=1);

namespace Simplex\DefinitionLoader;

use DI\ContainerBuilder as ContainerBuilder;

class ChainDefinitionLoader implements DefinitionLoader
{
    /** @var DefinitionLoader[] */
    private $loaders;

    /** @param DefinitionLoader[] $loaders */
    public function __construct(...$loaders)
    {
        foreach ($loaders as $loader) {
            $this->addLoader($loader);
        }
    }

    private function addLoader(DefinitionLoader $loader): void
    {
        $this->loaders[] = $loader;
    }

    public function load(ContainerBuilder $containerBuilder): void
    {
        foreach ($this->loaders as $loader) {
            $loader->load($containerBuilder);
        }
    }
}
