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
     * NullableStrategy constructor.
     * @param StrategyInterface $strategy
     */
    public function __construct(StrategyInterface $strategy)
    {
        $this->strategy = $strategy;
    }

    /**
     * @inheritDoc
     */
    public function extract($value, ?object $object = null)
    {
        if ($value === null) {
            return null;
        }

        return $this->strategy->extract($value, $object);
    }

    /**
     * @inheritDoc
     */
    public function hydrate($value, ?array $data)
    {
        if ($value === null) {
            return null;
        }

        return $this->strategy->hydrate($value, $data);
    }
}
