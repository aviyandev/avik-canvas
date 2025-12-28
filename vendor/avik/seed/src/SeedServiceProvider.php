<?php

declare(strict_types=1);

namespace Avik\Seed;

use Avik\Seed\Contracts\ServiceProvider;

class SeedServiceProvider implements ServiceProvider
{
    public function register(): void
    {
        // Intentionally empty
    }

    public function boot(): void
    {
        // Intentionally empty
    }
}
