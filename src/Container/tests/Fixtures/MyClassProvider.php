<?php

declare(strict_types=1);

namespace spaceonfire\Container\Fixtures;

use spaceonfire\Container\ServiceProvider\AbstractServiceProvider;

class MyClassProvider extends AbstractServiceProvider
{
    /**
     * @inheritDoc
     */
    public function provides(): array
    {
        return [
            MyClass::class,
        ];
    }

    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->getContainer()->add(MyClass::class, new MyClass());
    }
}
