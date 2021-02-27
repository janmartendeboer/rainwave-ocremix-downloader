<?php

declare(strict_types=1);

namespace Johmanx10\Rainwave\Music;

use IteratorIterator;

final class ArtistList extends IteratorIterator
{
    public function current(): Artist
    {
        return parent::current();
    }
}
