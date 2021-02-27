<?php

declare(strict_types=1);

namespace Johmanx10\Rainwave\Music;

final class Song
{
    public function __construct(
        public int $id,
        public string $title,
        public string $url,
        public ArtistList $artists,
        public AlbumList $albums
    ) {}
}
