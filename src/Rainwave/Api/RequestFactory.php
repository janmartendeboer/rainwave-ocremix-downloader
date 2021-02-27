<?php

declare(strict_types=1);

namespace Johmanx10\Rainwave\Api;

use Johmanx10\Rainwave\Station\Station;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamFactoryInterface;

class RequestFactory
{
    private const CONTENT_TYPE = 'application/x-www-form-urlencoded';

    public function __construct(
        private Station $station,
        private Authentication $authentication,
        private RequestFactoryInterface $requestFactory,
        private StreamFactoryInterface $streamFactory,
        private string $endpoint = 'https://rainwave.cc/api4/'
    ) {}

    /**
     * Create an API request for the given action and payload.
     *
     * @param string $action
     * @param array $payload
     *
     * @return RequestInterface
     */
    private function createRequest(
        string $action,
        array $payload = []
    ): RequestInterface {
        return $this
            ->requestFactory
            ->createRequest(
                'POST',
                $this->endpoint . $action
            )
            ->withHeader('content-type', static::CONTENT_TYPE)
            ->withBody(
                $this->streamFactory->createStream(
                    http_build_query(
                        array_replace_recursive(
                            [
                                'user_id' => $this->authentication->user,
                                'key' => $this->authentication->key,
                                'sid' => $this->station->id
                            ],
                            $payload
                        )
                    )
                )
            );
    }

    /**
     * @see https://rainwave.cc/api4/help/api4/all_faves
     *
     * @param int $perPage
     * @param int $pageStart
     *
     * @return RequestInterface
     */
    public function allFaves(
        int $perPage = 0,
        int $pageStart = 0
    ): RequestInterface {
        return $this->createRequest(
            'all_faves',
            array_filter(
                [
                    'per_page' => $perPage,
                    'page_start' => $pageStart
                ],
                fn (int $prop) => $prop >= 0
            )
        );
    }

    /**
     * @see https://rainwave.cc/api4/help/api4/song
     *
     * @param int $id
     * @param bool $allCategories
     *
     * @return RequestInterface
     */
    public function song(int $id, bool $allCategories = false): RequestInterface
    {
       return $this->createRequest(
           'song',
           array_filter(
               [
                   'id' => $id,
                   'all_categories' => $allCategories
               ]
           )
       );
    }
}
