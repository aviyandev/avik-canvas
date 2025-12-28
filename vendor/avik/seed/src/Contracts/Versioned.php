<?php

declare(strict_types=1);

namespace Avik\Seed\Contracts;

interface Versioned
{
    public function version(): string;
}
