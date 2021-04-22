<?php

declare(strict_types=1);

namespace spaceonfire\Container\Fixtures\ServiceProvider;

use spaceonfire\Container\Fixtures\MyClass;
use spaceonfire\Container\ServiceProvider\AbstractServiceProvider;

class MyClassProvider extends AbstractServiceProvider
{
    public function provides(): array
    {
        return [
            MyClass::class,
            'tag',
        ];
    }

    public function register(): void
    {
        $this->getContainer()->define(MyClass::class, new MyClass())->addTag('tag');
    }
}
