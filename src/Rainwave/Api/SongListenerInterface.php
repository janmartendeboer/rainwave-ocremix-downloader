<?php

declare(strict_types=1);

namespace Johmanx10\Rainwave\Api;

use Johmanx10\Rainwave\Music\Song;

interface SongListenerInterface
{
    public function __invoke(Song $song): void;
}
