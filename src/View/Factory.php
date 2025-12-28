<?php

declare(strict_types=1);

namespace Avik\Canvas\View;

use Avik\Canvas\Compiler\Compiler;
use Avik\Canvas\Engine\PhpEngine;
use Avik\Canvas\Exceptions\ViewNotFoundException;

final class Factory
{
    private array $shared = [];
    private array $composers = [];
    private Compiler $compiler;

    public function __construct(
        private string $viewsPath,
        private string $cachePath
    ) {
        $this->compiler = new Compiler();
    }

    public function directive(string $name, callable $handler): void
    {
        $this->compiler->directive($name, $handler);
    }

    public function share(string $key, mixed $value): void
    {
        $this->shared[$key] = $value;
    }

    public function exists(string $view): bool
    {
        $path = $this->viewsPath . '/' . str_replace('.', '/', $view) . '.avik.php';

        return is_file($path);
    }

    public function composer(string $view, callable $callback): void
    {
        $this->composers[$view][] = $callback;
    }

    public function make(string $view, array $data = []): View
    {
        $path = $this->viewsPath . '/' . str_replace('.', '/', $view) . '.avik.php';

        if (!is_file($path)) {
            throw new ViewNotFoundException($view);
        }

        // Apply composers
        foreach ($this->composers['*'] ?? [] as $callback) {
            $data = $callback($data);
        }

        foreach ($this->composers[$view] ?? [] as $callback) {
            $data = $callback($data);
        }

        $data = array_merge($this->shared, $data);

        return new View(
            $path,
            $data,
            $this->compiler,
            new PhpEngine(),
            $this->cachePath,
            $this->viewsPath
        );
    }
}
