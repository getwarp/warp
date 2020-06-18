<?php

declare(strict_types=1);

namespace spaceonfire\ValueObject\Integrations\HydratorStrategy;

use Laminas\Hydrator\Strategy\StrategyInterface;
use spaceonfire\ValueObject\BaseValueObject;
use Webmozart\Assert\Assert;

final class ValueObjectLaminasHydratorStrategy implements StrategyInterface
{
    /**
     * @var string|BaseValueObject
     */
    private $valueObjectClass;

    /**
     * ValueObjectLaminasHydratorStrategy constructor.
     * @param string|BaseValueObject $valueObjectClass
     */
    public function __construct(string $valueObjectClass)
    {
        Assert::subclassOf($valueObjectClass, BaseValueObject::class);
        $this->valueObjectClass = $valueObjectClass;
    }

    /**
     * @inheritDoc
     * @param BaseValueObject|mixed $value
     */
    public function extract($value, ?object $object = null)
    {
        $class = $this->valueObjectClass;

        if ($value instanceof $class) {
            return $value->value();
        }

        return $value;
    }

    /**
     * @inheritDoc
     */
    public function hydrate($value, ?array $data)
    {
        $class = $this->valueObjectClass;

        if ($value instanceof $class) {
            return $value;
        }

        return new $class($value);
    }
}
