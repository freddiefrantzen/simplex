<?php declare(strict_types=1);

/**
 * This file is part of the Simplex package.
 *
 * (c) Freddie Frantzen <freddie@freddiefrantzen.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Simplex\Tests;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Simplex\ConsoleApplication;
use Symfony\Component\Console\Command\Command;

class ConsoleApplicationTest extends TestCase
{
    public function test_it_adds_commands_to_symfony_console_application()
    {
        $container = $this->getStubbedContainer();

        $application = new ConsoleApplication($container);

        $symfonyApplication = $application->getSymfonyApplication();

        self::assertTrue($symfonyApplication->has('test:command'));
    }

    public function getStubbedContainer(): ContainerInterface
    {
        return new class implements ContainerInterface
        {
            public function get($id)
            {
                if ($id === 'console_commands') {
                    return [
                        new class extends Command {
                            protected function configure() {
                                $this->setName('test:command');
                            }
                        }
                    ];
                }
            }

            public function has($id)
            {
                if ($id === 'console_commands') {
                    return true;
                }

                return false;
            }
        };
    }
}
