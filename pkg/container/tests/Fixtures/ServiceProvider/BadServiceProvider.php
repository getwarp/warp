<?php

declare(strict_types=1);

namespace Warp\Container\Fixtures\ServiceProvider;

use Warp\Container\ServiceProvider\AbstractServiceProvider;

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
