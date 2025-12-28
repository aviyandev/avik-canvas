<?php

declare(strict_types=1);

namespace Avik\Essence\Config;

final class Config
{
    protected static ?Repository $repository = null;

    public static function setRepository(Repository $repository): void
    {
        self::$repository = $repository;
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        return self::$repository?->get($key, $default);
    }

    public static function has(string $key): bool
    {
        return self::$repository?->has($key) ?? false;
    }

    public static function all(): array
    {
        return self::$repository?->all() ?? [];
    }
}
