<?php

declare(strict_types=1);

namespace spaceonfire\Bridge\Cycle\Factory;

use spaceonfire\Container\FactoryAggregateInterface;
use Spiral\Core\FactoryInterface;

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
