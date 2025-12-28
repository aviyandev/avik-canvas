<?php

declare(strict_types=1);

namespace Avik\Essence\Support;

final class Str
{
    public static function startsWith(string $value, string $needle): bool
    {
        return str_starts_with($value, $needle);
    }

    public static function endsWith(string $value, string $needle): bool
    {
        return str_ends_with($value, $needle);
    }

    public static function slug(string $value): string
    {
        $value = strtolower(trim($value));
        $value = preg_replace('/[^a-z0-9]+/', '-', $value);
        return trim($value, '-');
    }
}
