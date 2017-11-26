<?php declare(strict_types = 1);

namespace Simplex\DefinitionLoader;

use DI\ContainerBuilder;

interface DefinitionLoader
{
    public function load(ContainerBuilder $containerBuilder): void;
}
