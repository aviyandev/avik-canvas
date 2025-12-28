<?php

declare(strict_types=1);

namespace Avik\Canvas\Runtime;

final class ViewRuntime
{
    private ?string $layout = null;
    private array $sections = [];
    private array $sectionStack = [];
    private string $viewsPath;

    public function __construct(string $viewsPath)
    {
        $this->viewsPath = $viewsPath;
    }

    /* -------- Layouts -------- */

    public function extend(string $layout): void
    {
        $this->layout = $layout;
    }

    /* -------- Sections -------- */

    public function start(string $name): void
    {
        $this->sectionStack[] = $name;
        ob_start();
    }

    public function end(): void
    {
        $name = array_pop($this->sectionStack);
        $this->sections[$name] = ob_get_clean();
    }

    public function section(string $name): string
    {
        return $this->sections[$name] ?? '';
    }

    /* -------- Components -------- */

    public function component(string $name): string
    {
        $path = $this->viewsPath . '/components/' . $name . '.avik.php';

        if (!is_file($path)) {
            throw new \RuntimeException("Canvas component [$name] not found.");
        }

        ob_start();
        include $path;
        return ob_get_clean();
    }

    /* -------- Final render -------- */

    public function hasLayout(): bool
    {
        return $this->layout !== null;
    }

    public function getLayout(): ?string
    {
        return $this->layout;
    }
}
