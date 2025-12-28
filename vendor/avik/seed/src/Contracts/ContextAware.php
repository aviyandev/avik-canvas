<?php

declare(strict_types=1);

namespace Avik\Seed\Contracts;

interface ContextAware
{
    public function setContext(array $context): void;
}
