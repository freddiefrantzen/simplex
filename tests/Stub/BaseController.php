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

use Psr\Http\Message\ResponseInterface;
use Simplex\Controller;

class BaseController implements Controller
{
    public static $baz;

    /** @var ResponseInterface */
    private $response;

    public function setBaz($baz)
    {
        self::$baz = $baz;
    }

    public function setResponse(ResponseInterface $response): void
    {
        $this->response = $response;
    }
}
