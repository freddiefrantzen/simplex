<?php declare(strict_types=1);

/**
 * This file is part of the Simplex package.
 *
 * (c) Freddie Frantzen <freddie@freddiefrantzen.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Simplex\Tests\Util;

use org\bovigo\vfs\vfsStream;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

trait VirtualFileSystemCapabilities
{
    /** @var string  */
    private $vfsProtocolIdentifier = 'vfs://';

    /** @var string */
    private $rootDirectory;

    private function createVirtualFilesystem(array $structure): void
    {
        $this->rootDirectory = $this->generateRandomString();

        vfsStream::setup($this->rootDirectory);
        vfsStream::create($structure);
    }

    private function generateRandomString(): string
    {
        return substr(str_shuffle(str_repeat('abcdefghijklmnopqrstuvwxyz', random_int(1, 10))),1, 10);
    }

    private function getVfsRoot(): string
    {
        return $this->vfsProtocolIdentifier . $this->rootDirectory;
    }

    private function destroyVirtualFilesystem(): void
    {
        if (!is_dir($this->getVfsRoot())) {
            return;
        }

        /** @var Finder $finder */
        $finder = (new Finder())->in($this->getVfsRoot())
            ->sort(function (SplFileInfo $fileInfoA, SplFileInfo $fileInfoB) {
                return substr_count($fileInfoA->getPathname(), '/') < substr_count($fileInfoB->getPathname(), '/');
            });

        foreach ($finder as $fileInfo) {
            if ($fileInfo->isFile()) {
                unlink($fileInfo->getPathname());
                continue;
            }

            rmdir($fileInfo->getPathname());
        }

        rmdir($this->getVfsRoot());
    }
}
