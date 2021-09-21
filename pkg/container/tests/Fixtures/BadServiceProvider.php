<?php

declare(strict_types=1);

namespace spaceonfire\Container\Fixtures;

use spaceonfire\Container\ServiceProvider\AbstractServiceProvider;

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
