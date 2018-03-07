<?php declare(strict_types = 1);

/**
 * This file is part of the Simplex package.
 *
 * (c) Freddie Frantzen <freddie@freddiefrantzen.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Simplex;

use DI\Container;
use DI\ContainerBuilder as PHPDIContainerBuilder;
use Simplex\DefinitionLoader\DefinitionLoader;

class ContainerBuilder
{
    private const CONTAINER_CLASS_SUFFIX = 'Container';

    private const CONTAINER_CLASS_FILE_EXTENSION = '.php';

    /** @var PHPDIContainerBuilder */
    private $phpDiContainerBuilder;

    /** @var DefinitionLoader */
    private $definitionLoader;

    /** @var string */
    private $environment;

    /** @var bool */
    private $compileContainer = false;

    /** @var \SplFileInfo */
    private $compiledContainerDirectory;

    /** @var string */
    private $compiledContainerClassName;

    public function __construct(
        PHPDIContainerBuilder $phpDiContainerBuilder,
        DefinitionLoader $definitionLoader,
        string $environment
    ) {
        $this->phpDiContainerBuilder = $phpDiContainerBuilder;
        $this->definitionLoader = $definitionLoader;
        $this->environment = $environment;
    }

    public function enableCompilation(\SplFileInfo $compiledContainerDirectory): void
    {
        $this->compileContainer = true;
        $this->compiledContainerDirectory = $compiledContainerDirectory;
        $this->compiledContainerClassName =  ucfirst($this->environment) . self::CONTAINER_CLASS_SUFFIX;

        $this->phpDiContainerBuilder->enableCompilation(
            $this->compiledContainerDirectory->getPathname(),
            $this->compiledContainerClassName
        );
    }

    public function build(): Container
    {
        if (!$this->compileContainer) {
            $this->definitionLoader->load($this->phpDiContainerBuilder);
            return $this->phpDiContainerBuilder->build();
        }

        return $this->buildWithCompilation();
    }

    private function buildWithCompilation(): Container
    {
        $compiledContainerClassFile = $this->compiledContainerDirectory->getPathname()
            . DIRECTORY_SEPARATOR
            . $this->compiledContainerClassName
            . self::CONTAINER_CLASS_FILE_EXTENSION;

        if (!file_exists($compiledContainerClassFile)) {
            $this->definitionLoader->load($this->phpDiContainerBuilder);
        }

        return $this->phpDiContainerBuilder->build();
    }
}
