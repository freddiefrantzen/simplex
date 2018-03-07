<?php declare(strict_types = 1);

/**
 * This file is part of the Simplex package.
 *
 * (c) Freddie Frantzen <freddie@freddiefrantzen.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Simplex\Tests\Stub;

use DI\ContainerBuilder;

final class PHPDIContainerBuilder extends ContainerBuilder
{
    /** @var bool */
    public $compilationEnabled = false;

    /** @var bool */
    public $definitionsLoaded = false;

    /** @var bool */
    public $containerBuilt = false;

    public function reset(): void
    {
        $this->compilationEnabled = false;
        $this->definitionsLoaded = false;
        $this->containerBuilt = false;
    }

    public function enableCompilation(string $directory, string $className = 'CompiledContainer'): ContainerBuilder
    {
        $this->compilationEnabled = true;

        parent::enableCompilation($directory, $className);

        return $this;
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function addDefinitions($definitions): ContainerBuilder
    {
        $this->definitionsLoaded = true;

        return $this;
    }

    public function build()
    {
        $this->containerBuilt = true;

        return parent::build();
    }
}
