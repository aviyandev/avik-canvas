<?php

declare(strict_types=1);

namespace Avik\Seed\Contracts;

interface Bootable
{
    public function boot(): void;
}
