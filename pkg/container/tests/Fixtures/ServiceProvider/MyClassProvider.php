<?php

declare(strict_types=1);

namespace Warp\Container\Fixtures\ServiceProvider;

use Warp\Container\Fixtures\MyClass;
use Warp\Container\ServiceProvider\AbstractServiceProvider;

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
