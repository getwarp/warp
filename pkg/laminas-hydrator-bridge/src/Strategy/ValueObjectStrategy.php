<?php

declare(strict_types=1);

namespace spaceonfire\Bridge\LaminasHydrator\Strategy;

use Laminas\Hydrator\Strategy\StrategyInterface;
use spaceonfire\ValueObject\AbstractValueObject;

/**
 * @template T of AbstractValueObject
 */
final class ValueObjectStrategy implements StrategyInterface
{
    /**
     * @var class-string<T>
     */
    private string $valueObjectClass;

    /**
     * @param class-string<T> $valueObjectClass
     */
    public function __construct(string $valueObjectClass)
    {
        if (!\is_subclass_of($valueObjectClass, AbstractValueObject::class)) {
            throw new \InvalidArgumentException(\sprintf(
                'Argument #2 ($valueObjectClass) expected to be subclass of %s. Got: %s.',
                AbstractValueObject::class,
                $valueObjectClass,
            ));
        }

        $this->valueObjectClass = $valueObjectClass;
    }

    /**
     * @inheritDoc
     * @param T $value
     */
    public function extract($value, ?object $object = null)
    {
        if ($value instanceof $this->valueObjectClass) {
            return $value->value();
        }

        return $value;
    }

    /**
     * @inheritDoc
     * @param array<string,mixed>|null $data
     * @return T
     */
    public function hydrate($value, ?array $data = null)
    {
        if ($value instanceof $this->valueObjectClass) {
            return $value;
        }

        return \call_user_func([$this->valueObjectClass, 'new'], $value);
    }
}
