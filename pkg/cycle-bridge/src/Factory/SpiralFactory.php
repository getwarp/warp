<?php

declare(strict_types=1);

namespace Warp\Bridge\Cycle\Factory;

use Spiral\Core\FactoryInterface;
use Warp\Container\FactoryAggregateInterface;

final class SpiralFactory implements FactoryInterface
{
    private FactoryAggregateInterface $factory;

    public function __construct(FactoryAggregateInterface $factory)
    {
        $this->factory = $factory;
    }

    /**
     * @template T
     * @param class-string<T> $alias
     * @param array<array-key,mixed> $parameters
     * @return T
     */
    public function make(string $alias, array $parameters = [])
    {
        return $this->factory->make($alias, $parameters);
    }
}
