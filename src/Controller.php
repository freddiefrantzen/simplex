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

abstract class Controller implements Httpstatuscodes
{
    /** @var UrlGeneratorInterface */
    private $urlGenerator;

    /** @var SerializerInterface */
    private $serializer;

    public function setUrlGenerator(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    public function setSerializer(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    public function jsonResponse(Response $response, $data = null, int $status = self::HTTP_OK): Response
    {
        if ($data !== null) {

            $serialized = $this->serializer->serialize($data, 'json');

            $response->getBody()->write($serialized);
        }

        return $response->withStatus($status);
    }

    public function noContentResponse(Response $response): Response
    {
        return $response->withStatus(self::HTTP_NO_CONTENT);
    }

    public function createdResponse(Response $response, string $resourceId): Response
    {
        return $response
            ->withStatus(self::HTTP_CREATED)
            ->withHeader('Location', $resourceId);
    }
}
