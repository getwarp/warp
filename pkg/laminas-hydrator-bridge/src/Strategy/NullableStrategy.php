<?php

declare(strict_types=1);

namespace spaceonfire\Bridge\LaminasHydrator\Strategy;

use Laminas\Hydrator\Strategy\StrategyInterface;

final class NullableStrategy implements StrategyInterface
{
    private StrategyInterface $strategy;

    /**
     * @var callable(mixed):bool
     */
    private $nullValuePredicate;

    /**
     * NullableStrategy constructor.
     * @param StrategyInterface $strategy
     * @param null|callable(mixed):bool $nullValuePredicate
     */
    public function __construct(StrategyInterface $strategy, ?callable $nullValuePredicate = null)
    {
        $this->strategy = $strategy;
        $this->nullValuePredicate = $nullValuePredicate ?? static fn ($value) => null === $value;
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
     * @param array<string,mixed>|null $data
     */
    public function hydrate($value, ?array $data = null)
    {
        if (($this->nullValuePredicate)($value)) {
            return null;
        }

        return $this->strategy->hydrate($value, $data);
    }
}
