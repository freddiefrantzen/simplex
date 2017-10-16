<?php declare(strict_types=1);

namespace Simplex;

use Psr\Http\Message\ResponseInterface;

interface Controller
{
    public function setResponse(ResponseInterface $response): void;
}
