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

use JMS\Serializer\SerializerInterface;
use Lukasoppermann\Httpstatus\Httpstatuscodes;
use Psr\Http\Message\ResponseInterface as Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

abstract class Controller
{
    const LOCATION_HEADER_NAME = 'Location';

    const JSON_FORMAT = 'json';

    /** @var UrlGeneratorInterface */
    protected $urlGenerator;

    /** @var SerializerInterface */
    protected $serializer;

    public function setUrlGenerator(UrlGeneratorInterface $urlGenerator): void
    {
        $this->urlGenerator = $urlGenerator;
    }

    public function setSerializer(SerializerInterface $serializer): void
    {
        $this->serializer = $serializer;
    }

    public function jsonResponse(Response $response, $data = null, int $status = Httpstatuscodes::HTTP_OK): Response
    {
        if ($data !== null) {

            $serialized = $this->serializer->serialize($data, self::JSON_FORMAT);

            $response->getBody()->write($serialized);
        }

        return $response->withStatus($status);
    }

    public function noContentResponse(Response $response): Response
    {
        return $response->withStatus(Httpstatuscodes::HTTP_NO_CONTENT);
    }

    public function createResponse(Response $response, string $resourceId): Response
    {
        return $response
            ->withStatus(Httpstatuscodes::HTTP_CREATED)
            ->withHeader(self::LOCATION_HEADER_NAME, $resourceId);
    }
}
