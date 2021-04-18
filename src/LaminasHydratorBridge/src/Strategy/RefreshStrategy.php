<?php

declare(strict_types=1);

namespace spaceonfire\LaminasHydratorBridge\Strategy;

use Laminas\Hydrator\Strategy\StrategyInterface;

final class RefreshStrategy implements StrategyInterface
{
    private $value;

    /**
     * RefreshStrategy constructor.
     * @param mixed $value
     */
    public function __construct($value = null)
    {
        $this->value = $value;
    }

    /**
     * @inheritDoc
     */
    public function extract($value, ?object $object = null)
    {
        return $this->value;
    }

    /**
     * @inheritDoc
     */
    public function hydrate($value, ?array $data)
    {
        return $this->value;
    }
}
