<?php

declare(strict_types=1);

namespace Avik\Canvas\Compiler;

final class Compiler
{
    private array $directives = [];

    public function directive(string $name, callable $handler): void
    {
        $this->directives[$name] = $handler;
    }

    public function compile(string $source): string
    {
        $source = $this->compileSyntax($source);
        $source = $this->compileCustomDirectives($source);
        $source = $this->compileSections($source);
        $source = $this->compileComponents($source);

        return $source;
    }

    private function compileCustomDirectives(string $source): string
    {
        foreach ($this->directives as $name => $handler) {
            $pattern = '/@' . preg_quote($name, '/') . '\s*(?:\((.*?)\))?/s';
            $source = preg_replace_callback($pattern, function ($matches) use ($handler) {
                $args = $matches[1] ?? null;
                return (string) $handler($args);
            }, $source);
        }

        return $source;
    }

    private function compileSyntax(string $s): string
    {
        // Escaped output: {{ $var }} or {{ var }}
        $s = preg_replace_callback('/\{\{\s*(.+?)\s*\}\}/s', function ($m) {
            $expr = trim($m[1]);
            if (preg_match('/^\w+$/', $expr)) {
                $expr = '$' . $expr;
            }
            return "<?= htmlspecialchars((string) ($expr), ENT_QUOTES, 'UTF-8') ?>";
        }, $s);

        // Raw output: {!! $var !!}
        $s = preg_replace_callback('/\{\!\!\s*(.+?)\s*\!\!\}/s', function ($m) {
            $expr = trim($m[1]);
            if (preg_match('/^\w+$/', $expr)) {
                $expr = '$' . $expr;
            }
            return "<?= $expr ?>";
        }, $s);

        // @each $items as $item
        $s = preg_replace('/@each\s+(.+?)\s+as\s+(\w+)/', '<?php foreach ($1 ?? [] as \$$2): ?>', $s);
        $s = str_replace('@endEach', '<?php endforeach; ?>', $s);

        // @show $condition
        $s = preg_replace('/@show\s+(.+?)(?:\s|$)/', '<?php if (!empty($1)): ?>', $s);
        $s = str_replace('@endshow', '<?php endif; ?>', $s);

        // Layouts
        $s = preg_replace('/@layout\s+[\'"]?(.+?)[\'"]?/', '<?php $this->extend("$1"); ?>', $s);
        $s = str_replace('@content', '<?php $this->start("content"); ?>', $s);
        $s = str_replace('@endcontent', '<?php $this->end(); ?>', $s);

        // Slots
        $s = preg_replace('/@slot\s*\(\s*[\'"]?(.+?)[\'"]?\s*\)/', '<?php $this->startSlot("$1"); ?>', $s);
        $s = str_replace('@endslot', '<?php $this->endSlot(); ?>', $s);
        $s = preg_replace('/@yieldSlot\s*\(\s*[\'"]?(.+?)[\'"]?(?:\s*,\s*[\'"]?(.*?)[\'"]?)?\s*\)/', '<?= $this->slot("$1", "$2" ?? "") ?>', $s);

        return $s;
    }

    private function compileSections(string $s): string
    {
        return $s; // Can be expanded later
    }

    private function compileComponents(string $s): string
    {
        // @component('name', $data)
        $s = preg_replace_callback(
            '/@component\s*\(\s*[\'"]?(.+?)[\'"]?\s*(?:,\s*(.+?))?\s*\)/',
            fn($m) => "<?php \$this->startComponent('{$m[1]}', " . ($m[2] ?? '[]') . "); ?>",
            $s
        );

        $s = str_replace('@endcomponent', '<?php echo $this->endComponent(); ?>', $s);

        return $s;
    }
}