<?php

declare(strict_types=1);

namespace Johmanx10\Rainwave\Music;

final class Artist
{
    public function __construct(
        public int $id,
        public string $name
    ) {}
}
