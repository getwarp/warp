<?php

declare(strict_types=1);

namespace Warp\Bridge\LaminasHydrator\Strategy;

use Laminas\Hydrator\Strategy\StrategyInterface;

/**
 * @template T
 */
final class RefreshStrategy implements StrategyInterface
{
    /**
     * @var T
     */
    private $value;

    /**
     * @param T $value
     */
    public function __construct($value = null)
    {
        $this->value = $value;
    }

    /**
     * @inheritDoc
     * @return T
     */
    public function extract($value, ?object $object = null)
    {
        return $this->value;
    }

    /**
     * @inheritDoc
     * @param array<string,mixed>|null $data
     * @return T
     */
    public function hydrate($value, ?array $data = null)
    {
        return $this->value;
    }
}
