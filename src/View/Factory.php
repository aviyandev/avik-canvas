<?php

declare(strict_types=1);

namespace Avik\Canvas\View;

use Avik\Canvas\Compiler\Compiler;
use Avik\Canvas\Engine\PhpEngine;

final class Factory
{
    public function __construct(
        private string $viewsPath,
        private string $cachePath
    ) {}

    public function make(string $view, array $data = []): View
    {
        $path = $this->viewsPath . '/' . str_replace('.', '/', $view) . '.avik.php';

        if (!is_file($path)) {
            throw new \RuntimeException("Canvas view [$view] not found.");
        }

        return new View(
            $path,
            $data,
            new Compiler(),
            new PhpEngine(),
            $this->cachePath,
            $this->viewsPath
        );
    }
}
