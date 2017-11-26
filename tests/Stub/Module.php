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

class Module implements \Simplex\Module
{
    public static $serviceDefinitionDirectory = null;

    public function getServiceDefinitionsDirectory(): ?string
    {
        return self::$serviceDefinitionDirectory;
    }
}
