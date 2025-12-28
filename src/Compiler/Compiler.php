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
        $source = $this->syntax($source);
        $source = $this->customDirectives($source);
        $source = $this->sections($source);
        $source = $this->components($source);

        return $source;
    }

    private function customDirectives(string $s): string
    {
        foreach ($this->directives as $name => $handler) {
            $pattern = '/@' . $name . '\s*(?:\((.*?)\))?/';
            $s = preg_replace_callback($pattern, function ($matches) use ($handler) {
                return $handler($matches[1] ?? null);
            }, $s);
        }

        return $s;
    }

    private function syntax(string $s): string
    {
        // 1. Escaped output: {{ $var }} or {{ var }}
        $s = preg_replace_callback('/\{\{\s*(.+?)\s*\}\}/s', function ($m) {
            $expr = trim($m[1]);
            if (preg_match('/^\w+$/', $expr)) {
                $expr = '$' . $expr;
            }
            return "<?= htmlspecialchars((string) ($expr), ENT_QUOTES, 'UTF-8') ?>";
        }, $s);

        // 2. Raw output: {!! $var !!} or {!! var !!}
        $s = preg_replace_callback('/\{\!\!\s*(.+?)\s*\!\!\}/s', function ($m) {
            $expr = trim($m[1]);
            if (preg_match('/^\w+$/', $expr)) {
                $expr = '$' . $expr;
            }
            return "<?= $expr ?>";
        }, $s);

        // 3. Loops: @each items as item
        $s = preg_replace_callback('/@each\s+([\w\->\[\]\'\"\$]+)\s+as\s+(\w+)/', function ($m) {
            $items = $m[1];
            if (preg_match('/^\w+$/', $items)) {
                $items = '$' . $items;
            }
            return "<?php foreach ($items ?? [] as \${$m[2]}): ?>";
        }, $s);

        $s = str_replace('@endEach', '<?php endforeach; ?>', $s);

        // 4. Conditionals: @show isAdmin
        $s = preg_replace_callback('/@show\s+([\w\->\[\]\'\"\$]+)/', function ($m) {
            $expr = $m[1];
            if (preg_match('/^\w+$/', $expr)) {
                $expr = '$' . $expr;
            }
            return "<?php if (!empty($expr)): ?>";
        }, $s);

        $s = str_replace('@endshow', '<?php endif; ?>', $s);

        // 5. Layouts
        $s = preg_replace('/@layout\s+[\'"]?([\w\.\-]+)[\'"]?/', '<?php $this->extend("$1"); ?>', $s);
        $s = str_replace(['@content', '@endcontent'], ['<?php $this->start("content"); ?>', '<?php $this->end(); ?>'], $s);

        // 6. Slots
        $s = preg_replace('/@slot\s*\(\s*[\'"]?([\w\-]+)[\'"]?\s*\)/', '<?php $this->startSlot("$1"); ?>', $s);
        $s = str_replace('@endslot', '<?php $this->endSlot(); ?>', $s);
        $s = preg_replace('/@yieldSlot\s*\(\s*[\'"]?([\w\-]+)[\'"]?\s*(?:,\s*[\'"]?(.*?)[\'"]?)?\s*\)/', '<?= $this->slot("$1", "$2") ?>', $s);

        // 7. Components
        $s = preg_replace_callback('/@component\s*\(\s*[\'"]?([\w\.\-]+)[\'"]?\s*(?:,\s*(.*?))?\s*\)/', function ($m) {
            $name = $m[1];
            $data = isset($m[2]) && trim($m[2]) !== '' ? trim($m[2]) : '[]';
            return "<?php \$this->startComponent('$name', $data); ?>";
        }, $s);

        $s = str_replace('@endcomponent', '<?php echo $this->endComponent(); ?>', $s);
        $s = preg_replace('/@component\s+([\w\.\-]+)/', '<?= $this->component("$1") ?>', $s);

        return $s;
    }

    // Future-proof hooks (do nothing now)
    private function sections(string $s): string
    {
        return $s;
    }
    private function components(string $s): string
    {
        return $s;
    }
}
