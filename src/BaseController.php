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

use JMS\Serializer\SerializerInterface;
use Lukasoppermann\Httpstatus\Httpstatuscodes;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

abstract class BaseController implements Controller
{
    const JSON_FORMAT = 'json';

    /** @var UrlGeneratorInterface */
    protected $urlGenerator;

    /** @var SerializerInterface */
    protected $serializer;

    /** @var ResponseInterface */
    protected $response;

    public function setUrlGenerator(UrlGeneratorInterface $urlGenerator): void
    {
        $this->urlGenerator = $urlGenerator;
    }

    public function setSerializer(SerializerInterface $serializer): void
    {
        $this->serializer = $serializer;
    }

    public function setResponse(ResponseInterface $response): void
    {
        $this->response = $response;
    }

    public function jsonResponse($data = null, int $status = Httpstatuscodes::HTTP_OK): Response
    {
        if ($data !== null) {

            $serialized = $this->serializer->serialize($data, self::JSON_FORMAT);

            $this->response->getBody()->write($serialized);
        }

        return $this->response->withStatus($status);
    }

    public function setHeader(string $headerName, string $headerValue)
    {
        $this->response = $this->response->withHeader($headerName, $headerValue);
    }
}
