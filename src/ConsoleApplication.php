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

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;

class ConsoleApplication
{
    /** @var Application */
    private $symfonyApplication;

    public function __construct(Kernel $kernel)
    {
        if (!$kernel->getContainer()->has('console_commands')) {
            $this->symfonyApplication = new Application();
        }

        $commands = $kernel->getContainer()->get('console_commands');

        $application = new Application();

        /** @var Command $command */
        foreach ($commands as $command) {
            $application->add($command);
        }

        if ($kernel->getContainer()->has('console_helper_set')) {
            $helperSet = $kernel->getContainer()->get('console_helper_set');
            $application->setHelperSet($helperSet);
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
