<?php

declare(strict_types=1);

namespace Avik\Canvas;

use Avik\Canvas\View\Factory;
use Avik\Seed\Contracts\ServiceProvider;

final class CanvasServiceProvider implements ServiceProvider
{
    public function __construct(private $app) {}

    public function register(): void
    {
        $this->app->singleton(Factory::class, function ($app) {
            $config = $app->get('config');

            return new Factory(
                viewsPath: $config->get('view.paths')[0] ?? base_path('views'),
                cachePath: $config->get('view.compiled') ?? base_path('storage/canvas/views')
            );
        });

        $this->app->alias(Factory::class, 'view');
    }

    public function boot(): void {}
}
