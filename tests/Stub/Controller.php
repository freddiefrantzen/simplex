<?php declare(strict_types=1);

/**
 * This file is part of the Simplex package.
 *
 * (c) Freddie Frantzen <freddie@freddiefrantzen.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Simplex\Tests\Stub;

use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response;

class Controller extends BaseController
{
    const EXPECTED_VALUE_ID = 'abc123';
    const EXPECTED_VALUE_FOO = 'bar';

    public static $wasCalledWthExpectedArgs = false;

    public function testAction(ServerRequestInterface $request, string $id, string $foo)
    {
        if ($id === self::EXPECTED_VALUE_ID && $foo === self::EXPECTED_VALUE_FOO) {
            self::$wasCalledWthExpectedArgs = true;
        }

        return new Response();
    }
}
