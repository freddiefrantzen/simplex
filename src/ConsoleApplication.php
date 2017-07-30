<?php declare(strict_types=1);

/**
 * This file is part of the Simplex package.
 *
 * (c) Freddie Frantzen <freddie@freddiefrantzen.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Simplex;

use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;

class ConsoleApplication
{
    /** @var Application */
    private $symfonyApplication;

    public function __construct(ContainerInterface $container)
    {
        if (!$container->has('console_commands')) {
            $this->symfonyApplication = new Application();
            return;
        }

        $application = new Application();

        if ($container->has('console_helper_set')) {
            $helperSet = $container->get('console_helper_set');
            $application->setHelperSet($helperSet);
        }

        $commands = $container->get('console_commands');

        /** @var Command $command */
        foreach ($commands as $command) {
            $application->add($command);
        }

        $this->symfonyApplication = $application;
    }

    public function run(): void
    {
        $this->symfonyApplication->run();
    }

    public function getSymfonyApplication(): Application
    {
        return $this->symfonyApplication;
    }
}
