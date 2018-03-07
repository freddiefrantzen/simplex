<?php declare(strict_types = 1);

/**
 * This file is part of the Simplex package.
 *
 * (c) Freddie Frantzen <freddie@freddiefrantzen.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Simplex\HttpMiddleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;

final class RegisterExceptionHandler
{
    /** @var bool */
    private $debugEnabled;

    /** @var string */
    private $editor;

    public function __construct(bool $debugEnabled, string $editor)
    {
        $this->debugEnabled = $debugEnabled;
        $this->editor = $editor;
    }

    public function __invoke(ServerRequestInterface $request, Response $response, callable $next)
    {
        $this->registerExceptionHandler();

        $response = $next($request, $response);

        return $response;
    }

    private function registerExceptionHandler(): void
    {
        if (!$this->debugEnabled) {
            return;
        }

        $whoops = new Run();

        $handler = new PrettyPageHandler();
        $handler->setEditor($this->editor);

        $whoops->pushHandler($handler);
        $whoops->register();
    }
}
