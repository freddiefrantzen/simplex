<?php declare(strict_types = 1);

namespace Simplex\DefinitionLoader;

use DI\ContainerBuilder;
use Simplex\ContainerKeys;
use Simplex\Module;
use Symfony\Component\Finder\Finder;

final class ModuleDefinitionLoader implements DefinitionLoader
{
    private const MODULES_FILENAME = 'modules.php';

    /** @var \SplFileInfo */
    private $configDirectory;

    public function __construct(\SplFileInfo $configDirectory)
    {
        $this->configDirectory = $configDirectory;
    }

    public function load(ContainerBuilder $containerBuilder): void
    {
        $modules = $this->getModules();

        foreach ($modules as $module) {
            $this->addModuleToContainer($module, $containerBuilder);
            $this->loadModuleDefinitions($module, $containerBuilder);
        }
    }

    /** @return Module[] */
    private function getModules(): array
    {
        $modulesFile = new \SplFileInfo(
            $this->configDirectory->getPathname() . DIRECTORY_SEPARATOR . self::MODULES_FILENAME
        );

        if (!$modulesFile->isFile() || !$modulesFile->isReadable()) {
            throw new \RuntimeException('Could not read modules file ' . $modulesFile->getRealPath());
        }

        $modules = include $modulesFile;

        if (!is_array($modules)) {
            throw new \LogicException('Expected ' . $modulesFile->getPathname() . ' to return an array');
        }

        return $modules;
    }

    private function addModuleToContainer(Module $module, ContainerBuilder $containerBuilder): void
    {
        $containerBuilder->addDefinitions([
            get_class($module) => \DI\create(get_class($module)),
            ContainerKeys::MODULES => \DI\add([
                \DI\get(get_class($module)),
            ]),
        ]);
    }

    private function loadModuleDefinitions(Module $module, ContainerBuilder $containerBuilder): void
    {
        $definitionsDirectoryPath = $module->getServiceDefinitionsDirectory();

        if (null === $definitionsDirectoryPath) {
            return;
        }

        $definitionsDirectory = $this->getModuleDefinitionsDirectory(
            rtrim($definitionsDirectoryPath, DIRECTORY_SEPARATOR)
        );

        $finder = new Finder();
        $finder->files()->depth(0)->in($definitionsDirectory->getPathname());

        foreach ($finder as $file) {
            $containerBuilder->addDefinitions($file->getPathname());
        }
    }

    private function getModuleDefinitionsDirectory(string $definitionsDirectoryPath): \SplFileInfo
    {
        $definitionsDirectory = new \SplFileInfo(rtrim($definitionsDirectoryPath, DIRECTORY_SEPARATOR));

        if (!$definitionsDirectory->isDir() || !$definitionsDirectory->isReadable()) {
            throw new \RuntimeException(
                'Invalid module definitions directory path ' . $definitionsDirectory->getRealPath()
            );
        }

        return $definitionsDirectory;
    }
}
