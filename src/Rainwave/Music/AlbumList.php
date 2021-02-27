<?php

declare(strict_types=1);

namespace Johmanx10\Rainwave\Music;

use IteratorIterator;

final class AlbumList extends IteratorIterator
{
    public function current(): Album
    {
        return parent::current();
    }
}
