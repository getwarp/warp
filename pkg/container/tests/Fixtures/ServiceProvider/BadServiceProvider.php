<?php

declare(strict_types=1);

namespace spaceonfire\Container\Fixtures\ServiceProvider;

use spaceonfire\Container\ServiceProvider\AbstractServiceProvider;

class BadServiceProvider extends AbstractServiceProvider
{
    public function provides(): array
    {
        return ['bad'];
    }

    public function register(): void
    {
    }
}
