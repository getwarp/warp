<?php

declare(strict_types=1);

namespace spaceonfire\Type\Factory;

use spaceonfire\Type\Exception\TypeNotSupportedException;
use spaceonfire\Type\TypeInterface;

final class TypeFactoryAggregate implements TypeFactoryInterface
{
    use TypeFactoryTrait;

    /**
     * @var TypeFactoryInterface[]
     */
    private array $factories;

    public function __construct(TypeFactoryInterface ...$factories)
    {
        $this->factories = $factories;
    }

    public static function default(): self
    {
        return new self(...self::defaultFactories());
    }

    /**
     * @return TypeFactoryInterface[]
     */
    public static function defaultFactories(): iterable
    {
        yield new CollectionTypeFactory();
        yield new GroupTypeFactory();
        yield new ConjunctionTypeFactory();
        yield new DisjunctionTypeFactory();
        yield new InstanceOfTypeFactory();
        yield new BuiltinTypeFactory();
        yield new MixedTypeFactory();
        yield new VoidTypeFactory();
    }

    public function supports(string $type): bool
    {
        foreach ($this->factories as $factory) {
            $factory->setParent($this->parent ?? $this);

            if ($factory->supports($type)) {
                return true;
            }
        }

        return false;
    }

    public function make(string $type): TypeInterface
    {
        foreach ($this->factories as $factory) {
            $factory->setParent($this->parent ?? $this);

            if ($factory->supports($type)) {
                return $factory->make($type);
            }
        }

        throw new TypeNotSupportedException($type);
    }
}
