<?php

declare(strict_types=1);

namespace Warp\Container\Fixtures;

use Warp\Container\ServiceProvider\AbstractServiceProvider;

class MyClassProvider extends AbstractServiceProvider
{
    /**
     * @inheritDoc
     */
    public function provides(): array
    {
        return [
            MyClass::class,
            'tag',
        ];
    }

    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->getContainer()->add(MyClass::class, new MyClass())->addTag('tag');
    }
}
