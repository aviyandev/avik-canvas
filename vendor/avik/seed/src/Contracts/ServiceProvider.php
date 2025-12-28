<?php

declare(strict_types=1);

namespace Avik\Seed\Contracts;

interface ServiceProvider
{
    public function register(): void;

    public function boot(): void;
}
