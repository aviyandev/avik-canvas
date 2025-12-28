<?php

declare(strict_types=1);

namespace Avik\Canvas\Engine;

final class PhpEngine
{
    public function render(string $__path, array $__data, object $__runtime): string
    {
        extract($__data, EXTR_SKIP);

        ob_start();

        $renderer = function () use ($__path) {
            include $__path;
        };

        // Bind runtime as $this inside view
        $renderer = $renderer->bindTo($__runtime, $__runtime);

        $renderer();

        return ob_get_clean();
    }
}
