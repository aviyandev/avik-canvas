<?php

declare(strict_types=1);

namespace Avik\Canvas\Compiler;

final class Compiler
{
    public function compile(string $source): string
    {
        $source = $this->syntax($source);
        $source = $this->sections($source);
        $source = $this->components($source);

        return $source;
    }

    private function syntax(string $s): string
    {
        $map = [
            // escaped output
            '/\{\{\s*(.+?)\s*\}\}/s'
            => '<?= htmlspecialchars($1, ENT_QUOTES, "UTF-8") ?>',

            // raw output
            '/\{\!\!\s*(.+?)\s*\!\!\}/s'
            => '<?= $1 ?>',

            // loop
            '/@each\s+(\w+)\s+as\s+(\w+)/'
            => '<?php foreach ($$1 as $$2): ?>',

            '/@endEach/'
            => '<?php endforeach; ?>',

            // conditional (truthy)
            '/@show\s+(\w+)/'
            => '<?php if (!empty($$1)): ?>',

            '/@endshow/'
            => '<?php endif; ?>',

            // layout
            '/@layout\s+(\w+)/'
            => '<?php $this->extend("$1"); ?>',

            // content section
            '/@content/'
            => '<?php $this->start("content"); ?>',

            '/@endcontent/'
            => '<?php $this->end(); ?>',

            // component
            '/@component\s+(\w+)/'
            => '<?= $this->component("$1") ?>',
        ];

        return preg_replace(
            array_keys($map),
            array_values($map),
            $s
        );
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
