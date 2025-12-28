<?php

namespace Avik\Seed\Contracts;

interface Middleware
{
    public function handle(mixed $request, \Closure $next): mixed;
}
