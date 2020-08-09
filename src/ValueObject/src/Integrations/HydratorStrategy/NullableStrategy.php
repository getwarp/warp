<?php

declare(strict_types=1);

namespace spaceonfire\ValueObject\Integrations\HydratorStrategy;

use Laminas\Hydrator\Strategy\StrategyInterface;

final class NullableStrategy implements StrategyInterface
{
    /**
     * @var StrategyInterface
     */
    private $strategy;
    /**
     * @var callable
     */
    private $nullValuePredicate;

    /**
     * NullableStrategy constructor.
     * @param StrategyInterface $strategy
     * @param callable|null $nullValuePredicate
     */
    public function __construct(StrategyInterface $strategy, ?callable $nullValuePredicate = null)
    {
        $this->strategy = $strategy;
        $this->nullValuePredicate = $nullValuePredicate ?? [$this, 'defaultNullValuePredicate'];
    }

    /**
     * Default null value predicate
     * @param mixed $value
     * @return bool
     */
    public function defaultNullValuePredicate($value): bool
    {
        return $value === null;
    }

    /**
     * @inheritDoc
     */
    public function extract($value, ?object $object = null)
    {
        if (($this->nullValuePredicate)($value)) {
            return null;
        }

        return $this->strategy->extract($value, $object);
    }

    /**
     * @inheritDoc
     */
    public function hydrate($value, ?array $data)
    {
        if (($this->nullValuePredicate)($value)) {
            return null;
        }

        return $this->strategy->hydrate($value, $data);
    }
}
