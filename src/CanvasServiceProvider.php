<?php

declare(strict_types=1);

namespace Avik\Canvas;

use Avik\Ignite\Application;
use Avik\Seed\Contracts\ServiceProvider;
use Avik\Canvas\View\Factory;
use Avik\Essence\Config\Config;

final class CanvasServiceProvider implements ServiceProvider
{
    public function __construct(private Application $app) {}

    public function register(): void
    {
        $this->app->singleton(Factory::class, function () {
            $viewsPath = Config::get('view.paths.0') 
                ?? $this->app->basePath('views');

            $cachePath = Config::get('view.compiled') 
                ?? $this->app->basePath('storage/framework/views');

            // Ensure cache directory exists
            if (!is_dir($cachePath)) {
                mkdir($cachePath, 0755, true);
            }

            return new Factory(
                viewsPath: $viewsPath,
                cachePath: $cachePath
            );
        });

        // Alias for easy access via app('view')
        $this->app->alias(Factory::class, 'view');
    }

    public function boot(): void
    {
        // Optional: Register default directives or shared data here
    }
}