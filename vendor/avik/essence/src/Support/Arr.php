<?php

declare(strict_types=1);

namespace Avik\Essence\Support;

final class Arr
{
    public static function get(array $array, string $key, mixed $default = null): mixed
    {
        return $array[$key] ?? $default;
    }

    public static function has(array $array, string $key): bool
    {
        return array_key_exists($key, $array);
    }
}
