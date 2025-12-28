<?php

declare(strict_types=1);

namespace Avik\Canvas\Exceptions;

use RuntimeException;

final class ComponentNotFoundException extends RuntimeException
{
    public function __construct(string $component)
    {
        parent::__construct("Canvas component [$component] not found.");
    }
}
