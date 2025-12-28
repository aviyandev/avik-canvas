<?php

namespace Avik\Seed\Contracts;

interface Container
{
    public function bind(string $abstract, string|\Closure $concrete): void;
    public function singleton(string $abstract, string|\Closure $concrete): void;
    public function make(string $abstract, array $parameters = []): mixed;
}
