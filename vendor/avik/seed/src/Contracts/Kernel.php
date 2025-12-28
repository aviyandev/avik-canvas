<?php

namespace Avik\Seed\Contracts;

interface Kernel
{
    public function handle(mixed $request): mixed;
}
