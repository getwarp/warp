<?php

declare(strict_types=1);

namespace Warp\Container\Fixtures;

use Warp\Container\ServiceProvider\AbstractServiceProvider;

class BadServiceProvider extends AbstractServiceProvider
{
    /**
     * @inheritDoc
     */
    public function provides(): array
    {
        return ['bad'];
    }

    /**
     * @inheritDoc
     */
    public function register(): void
    {
    }
}
