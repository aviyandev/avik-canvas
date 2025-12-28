<?php

declare(strict_types=1);

namespace Avik\Canvas\Exceptions;

use RuntimeException;

final class ViewNotFoundException extends RuntimeException
{
    public function __construct(string $view)
    {
        parent::__construct("Canvas view [$view] not found.");
    }
}
