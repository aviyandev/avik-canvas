<?php

declare(strict_types=1);

namespace Avik\Canvas\Engine;

final class PhpEngine
{
    public function render(string $__path, array $__data, object $__runtime): string
    {
        $render = function () use ($__path, $__data) {
            extract($__data, EXTR_SKIP);
            include $__path;
        };

        $render = $render->bindTo($__runtime, $__runtime);

        ob_start();
        $render();
        return ob_get_clean() ?: '';
    }
}