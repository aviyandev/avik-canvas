<?php

declare(strict_types=1);

namespace Avik\Essence\Config;

use Avik\Seed\Contracts\Arrayable;

final class Repository implements Arrayable
{
    protected array $items = [];

    public function __construct(array $items = [])
    {
        $this->items = $items;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->items[$key] ?? $default;
    }

    public function has(string $key): bool
    {
        return array_key_exists($key, $this->items);
    }

    public function set(string $key, mixed $value): void
    {
        $this->items[$key] = $value;
    }

    public function all(): array
    {
        return $this->items;
    }

    public function toArray(): array
    {
        return $this->items;
    }
}
