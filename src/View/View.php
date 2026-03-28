<?php

declare(strict_types=1);

namespace Avik\Canvas\View;

use Avik\Canvas\Compiler\Compiler;
use Avik\Canvas\Engine\PhpEngine;
use Avik\Canvas\Runtime\ViewRuntime;

final class View
{
    public function __construct(
        private string $path,
        private array $data,
        private Compiler $compiler,
        private PhpEngine $engine,
        private string $cachePath,
        private string $viewsPath
    ) {}

    public function render(): string
    {
        $runtime = new ViewRuntime(
            $this->viewsPath,
            fn(string $path) => $this->compileFile($path)
        );

        $runtime->setData($this->data);

        $compiledPath = $this->compileFile($this->path);

        $content = $this->engine->render($compiledPath, $this->data, $runtime);

        if ($runtime->hasLayout()) {
            $layoutPath = $this->viewsPath . '/' . $runtime->getLayout() . '.avik.php';
            $compiledLayout = $this->compileFile($layoutPath);

            return $this->engine->render(
                $compiledLayout,
                array_merge($this->data, ['content' => $content]),
                $runtime
            );
        }

        return $content;
    }

    private function compileFile(string $path): string
    {
        $hash = md5($path . filemtime($path));
        $compiled = $this->cachePath . '/' . $hash . '.php';

        if (!is_file($compiled)) {
            $source = file_get_contents($path);
            $php = $this->compiler->compile($source);
            file_put_contents($compiled, $php);
        }

        return $compiled;
    }
}