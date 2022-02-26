<?php

declare(strict_types=1);

namespace spaceonfire\Type\Cast;

use spaceonfire\Type\TypeInterface;

/**
 * @implements \IteratorAggregate<CasterFactoryInterface>
 */
final class CasterFactoryAggregate implements CasterFactoryInterface, \IteratorAggregate
{
    /**
     * @var CasterFactoryInterface[]
     */
    private array $factories = [];

    public function __construct(CasterFactoryInterface ...$factories)
    {
        foreach ($factories as $factory) {
            $this->add($factory);
        }
    }

    public function add(CasterFactoryInterface $factory, CasterFactoryInterface ...$factories): void
    {
        foreach ([$factory, ...$factories] as $f) {
            if ($f instanceof CasterFactoryAwareInterface) {
                $f->setFactory($this);
            }

            $this->factories[] = $f;
        }
    }

    public function make(TypeInterface $type): ?CasterInterface
    {
        foreach ($this->factories as $factory) {
            $caster = $factory->make($type);

            if (null !== $caster) {
                return $caster;
            }
        }

        return null;
    }

    /**
     * @return \ArrayIterator<int,CasterFactoryInterface>
     */
    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->factories);
    }

    public static function default(): self
    {
        return new self(...self::defaultFactories());
    }

    /**
     * @return \Generator<CasterFactoryInterface>
     */
    public static function defaultFactories(): \Generator
    {
        yield new UnionTypeCasterFactory();
        yield new ScalarCasterFactory();
        yield new NullCasterFactory();
    }
}
