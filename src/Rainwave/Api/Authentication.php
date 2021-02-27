<?php

declare(strict_types=1);

namespace Johmanx10\Rainwave\Api;

final class Authentication
{
    public function __construct(public int $user, public string $key) {}

    /**
     * Create an authentication instance from environment variables.
     *
     * @param string $userId
     * @param string $apiKey
     *
     * @return static
     */
    public static function fromEnv(
        string $userId = 'RAINWAVE_USER_ID',
        string $apiKey = 'RAINWAVE_API_KEY'
    ): self {
        return new self(
            (int)$_ENV[$userId],
            (string)$_ENV[$apiKey]
        );
    }
}
