<?php

declare(strict_types=1);

namespace Warp\Container\Factory\Reflection;

use Psr\Container\ContainerInterface;
use Warp\Container\ContainerAwareInterface;
use Warp\Container\ContainerAwareTrait;
use Warp\Container\Exception\NotFoundException;
use Warp\Container\Factory\FactoryOptions;
use Warp\Container\FactoryAggregateInterface;
use Warp\Container\FactoryInterface;

final class ReflectionFactoryAggregate implements FactoryAggregateInterface, ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function __construct(?ContainerInterface $container = null)
    {
        $this->setContainer($container);
    }

    public function hasFactory(string $class): bool
    {
        return \class_exists($class);
    }

    /**
     * @inheritDoc
     * @template T of object
     * @param class-string<T> $class
     * @return FactoryInterface<T>
     */
    public function getFactory(string $class): FactoryInterface
    {
        if (!$this->hasFactory($class)) {
            throw NotFoundException::factory($class);
        }

        return new ReflectionFactory($class, $this->getContainer());
    }

    /**
     * @inheritDoc
     * @template T of object
     * @param class-string<T> $class
     * @return T
     */
    public function make(string $class, $options = null)
    {
        return $this->getFactory($class)->make(FactoryOptions::wrap($options));
    }
}
