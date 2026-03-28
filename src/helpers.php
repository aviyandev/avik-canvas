<?php

declare(strict_types=1);

use Avik\Canvas\View\Factory;

if (!function_exists('view')) {
    /**
     * Render a Canvas view.
     *
     * @param  string  $view
     * @param  array   $data
     * @return string
     */
    function view(string $view, array $data = []): string
    {
        /** @var Factory $factory */
        $factory = app('view');

        return $factory->make($view, $data)->render();
    }
}

if (!function_exists('app')) {
    /**
     * Get the container instance or resolve a binding.
     */
    function app(?string $abstract = null): mixed
    {
        $container = \Avik\Crate\Container::getInstance();

        return $abstract === null 
            ? $container 
            : $container->make($abstract);
    }
}