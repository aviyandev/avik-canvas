<?php

declare(strict_types=1);

namespace Avik\Canvas\Runtime;

use Avik\Canvas\Exceptions\ComponentNotFoundException;

final class ViewRuntime
{
    private ?string $layout = null;
    private array $sections = [];
    private array $sectionStack = [];
    private array $slots = [];
    private array $slotStack = [];
    private array $componentStack = [];
    private array $componentSlots = [];
    private array $data = [];

    public function __construct(
        private string $viewsPath,
        private \Closure $compiler
    ) {}

    public function setData(array $data): void
    {
        $this->data = $data;
    }

    // Layouts
    public function extend(string $layout): void
    {
        $this->layout = $layout;
    }

    public function hasLayout(): bool
    {
        return $this->layout !== null;
    }

    public function getLayout(): ?string
    {
        return $this->layout;
    }

    // Sections
    public function start(string $name): void
    {
        $this->sectionStack[] = $name;
        ob_start();
    }

    public function end(): void
    {
        $name = array_pop($this->sectionStack);
        $this->sections[$name] = ob_get_clean() ?? '';
    }

    public function section(string $name): string
    {
        return $this->sections[$name] ?? '';
    }

    // Slots
    public function startSlot(string $name): void
    {
        $this->slotStack[] = $name;
        ob_start();
    }

    public function endSlot(): void
    {
        $name = array_pop($this->slotStack);
        $this->slots[$name] = ob_get_clean() ?? '';
    }

    public function slot(string $name, string $default = ''): string
    {
        return $this->slots[$name] ?? $default;
    }

    // Components
    public function startComponent(string $name, array $data = []): void
    {
        $this->componentStack[] = [$name, $data];
        $this->componentSlots[] = $this->slots;
        $this->slots = [];
    }

    public function endComponent(): string
    {
        [$name, $data] = array_pop($this->componentStack);
        $html = $this->component($name, $data);
        $this->slots = array_pop($this->componentSlots) ?? [];
        return $html;
    }

    public function component(string $name, array $data = []): string
    {
        $path = $this->viewsPath . '/components/' . $name . '.avik.php';

        if (!is_file($path)) {
            throw new ComponentNotFoundException($name);
        }

        $compiledPath = ($this->compiler)($path);

        $render = function () use ($compiledPath, $data) {
            $vars = array_merge($this->data, $this->slots, $data);
            extract($vars, EXTR_SKIP);
            include $compiledPath;
        };

        $render = $render->bindTo($this, $this);

        ob_start();
        $render();
        return ob_get_clean() ?? '';
    }
}