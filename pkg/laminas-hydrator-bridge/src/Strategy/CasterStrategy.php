<?php

declare(strict_types=1);

namespace spaceonfire\Bridge\LaminasHydrator\Strategy;

use Laminas\Hydrator\Strategy\StrategyInterface;
use spaceonfire\Type\Cast\CasterInterface;

final class CasterStrategy implements StrategyInterface
{
    private CasterInterface $hydrateCast;

    private CasterInterface $extractCast;

    public function __construct(CasterInterface $hydrateCast, ?CasterInterface $extractCast = null)
    {
        $this->hydrateCast = $hydrateCast;
        $this->extractCast = $extractCast ?? $hydrateCast;
    }

    public function extract($value, ?object $object = null)
    {
        return $this->extractCast->cast($value);
    }

    /**
     * @inheritDoc
     * @param array<string,mixed>|null $data
     */
    public function hydrate($value, ?array $data = null)
    {
        return $this->hydrateCast->cast($value);
    }
}
