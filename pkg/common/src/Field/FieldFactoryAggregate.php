<?php

declare(strict_types=1);

namespace Warp\Common\Field;

/**
 * @implements \IteratorAggregate<FieldFactoryInterface>
 */
final class FieldFactoryAggregate implements FieldFactoryInterface, \IteratorAggregate
{
    /**
     * @var FieldFactoryInterface[]
     */
    private array $factories;

    public function __construct(FieldFactoryInterface ...$factories)
    {
        $this->factories = $factories;
    }

    public static function default(): self
    {
        return new self(
            new PropertyAccessFieldFactory(),
            new YiiFieldFactory(),
            new DefaultFieldFactory(),
        );
    }

    public function enabled(): bool
    {
        foreach ($this->factories as $factory) {
            if ($factory->enabled()) {
                return true;
            }
        }

        return false;
    }

    public function make(string $field): FieldInterface
    {
        foreach ($this->factories as $factory) {
            if ($factory->enabled()) {
                return $factory->make($field);
            }
        }

        throw new \RuntimeException('No enabled factories.');
    }

    /**
     * @return \Traversable<FieldFactoryInterface>
     */
    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->factories);
    }
}
