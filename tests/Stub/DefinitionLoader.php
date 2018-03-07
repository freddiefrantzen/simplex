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
use Simplex\DefinitionLoader\DefinitionLoader as DefinitionLoaderInterface;

final class DefinitionLoader implements DefinitionLoaderInterface
{
    public $wasCalled = false;

    public function reset()
    {
        $this->wasCalled = false;
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function load(ContainerBuilder $containerBuilder): void
    {
        $this->wasCalled = true;
    }
}
