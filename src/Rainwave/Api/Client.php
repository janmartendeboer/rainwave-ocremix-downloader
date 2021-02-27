<?php

declare(strict_types=1);

namespace Johmanx10\Rainwave\Api;

use ArrayIterator;
use Johmanx10\Rainwave\Music\Album;
use Johmanx10\Rainwave\Music\AlbumList;
use Johmanx10\Rainwave\Music\Artist;
use Johmanx10\Rainwave\Music\ArtistList;
use Johmanx10\Rainwave\Music\Song;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;

final class Client
{
    public function __construct(
        public ClientInterface $client,
        public RequestFactory $factory
    ) {}

    private static function parseAlbum(array $album): Album
    {
        return new Album($album['id'], $album['name'], $album['art']);
    }

    private static function parseArtist(array $artist): Artist
    {
        return new Artist($artist['id'], $artist['name']);
    }

    private function process(RequestInterface $request): array
    {
        return json_decode(
            $this
                ->client
                ->sendRequest($request)
                ->getBody()
                ->getContents(),
            true
        );
    }

    /**
     * Get the song with the given ID.
     *
     * @param int $id
     *
     * @return Song|null
     */
    public function getSong(int $id): ?Song
    {
        $data = $this->process(
            $this->factory->song($id)
        );

        if (!array_key_exists('song', $data)) {
            return null;
        }

        $song = $data['song'];

        return new Song(
            $song['id'],
            $song['title'],
            $song['url'],
            new ArtistList(
                new ArrayIterator(
                    array_map(
                        fn (array $artist) => self::parseArtist($artist),
                        $song['artists']
                    )
                )
            ),
            new AlbumList(
                new ArrayIterator(
                    array_map(
                        fn (array $album) => self::parseAlbum($album),
                        $song['albums']
                    )
                )
            )
        );
    }

    /**
     * Walk over each song listed under favorites.
     *
     * @param callable|SongListenerInterface $listener
     */
    public function walkFavorites(callable|SongListenerInterface $listener): void
    {
        static $size = 100;
        $page = 0;

        $seen = [];

        do {
            $data = $this->process(
                $this->factory->allFaves($size, ++$page)
            );

            foreach ($data['all_faves'] ?? [] as $favorite) {
                if (array_key_exists($favorite['id'], $seen)) {
                    continue;
                }

                $seen[$favorite['id']] = true;
                $song = $this->getSong($favorite['id']);

                if ($song !== null) {
                    $listener($song);
                }
            }
        } while (!empty($data['all_faves']));
    }
}
