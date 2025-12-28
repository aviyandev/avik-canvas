<?php

declare(strict_types=1);

namespace Avik\Seed\Contracts;

interface Terminable
{
    public function terminate(mixed $request, mixed $response): void;
}
