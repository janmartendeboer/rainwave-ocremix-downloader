<?php

declare(strict_types=1);

namespace Johmanx10\Rainwave\Station;

/**
 * @see https://rainwave.cc/api4/
 */
final class Station
{
    public const STATIONS = [
        1 => 'Game',
        2 => 'OC ReMix',
        3 => 'Covers',
        4 => 'Chiptunes',
        5 => 'All'
    ];

    public function __construct(public int $id) {}

    /**
     * @return static
     */
    public static function OCReMix(): self
    {
        return new self(2);
    }
}
