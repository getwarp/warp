<?php

declare(strict_types=1);

namespace spaceonfire\Container\Fixtures;

use spaceonfire\Container\Exception\NotFoundException;
use spaceonfire\Container\FactoryAggregateInterface;
use spaceonfire\Container\FactoryInterface;

final class ArrayFactoryAggregate implements FactoryAggregateInterface
{
    private array $factories;

    public function __construct(array $factories)
    {
        $this->factories = $factories;
    }

    public function hasFactory(string $class): bool
    {
        return isset($this->factories[$class]);
    }

    public function getFactory(string $class): FactoryInterface
    {
        if (isset($this->factories[$class])) {
            return $this->factories[$class];
        }

        throw NotFoundException::factory($class);
    }

    public function make(string $class, $options = null)
    {
        return $this->getFactory($class)->make($options);
    }
}
