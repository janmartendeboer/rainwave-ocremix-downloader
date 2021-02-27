<?php

declare(strict_types=1);

namespace Johmanx10\Ocremix;

use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\ResponseInterface;

final class Downloader
{
    public const HEADER_MIRRORS = 'X-Alternative-Mirrors';

    public function __construct(
        private ClientInterface $client,
        private RequestFactoryInterface $requestFactory,
        private string $endpoint = 'https://ocremix.org/'
    ) {}

    /**
     * Download the remix file for the given remix id.
     *
     * @param string $id
     *
     * @return ResponseInterface
     */
    public function downloadRemix(string $id): ResponseInterface
    {
        $response = $this->client->sendRequest(
            $this->requestFactory->createRequest(
                'GET',
                sprintf('%s/remix/%s', $this->endpoint, $id)
            )
        );

        if ($response->getStatusCode() !== 200) {
            return $response;
        }

        if (
            preg_match_all(
                '#href="(?P<url>https://[^"]+.mp3)"#',
                $response->getBody()->getContents(),
                $matches
            ) < 1
        ) {
            return $response;
        }

        $mirrors = $matches['url'] ?? [];

        foreach ($mirrors as $mirror) {
            $response = $this->client->sendRequest(
                $this->requestFactory->createRequest('GET', $mirror)
            );

            if ($response->getStatusCode() === 200) {
                break;
            }
        }

        return $response->withAddedHeader(self::HEADER_MIRRORS, $mirrors);
    }
}
