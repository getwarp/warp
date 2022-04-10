<?php

declare(strict_types=1);

namespace Warp\Bridge\LaminasHydrator;

use Laminas\Hydrator\HydratorInterface;

trait HydrateConstructorTrait
{
    /**
     * @param array<string,mixed> $config
     */
    public function __construct(array $config = [])
    {
        $this->hydrate($config);
    }

    /**
     * @return array<string,mixed>
     */
    public function getArrayCopy(): array
    {
        return static::hydrator()->extract($this);
    }

    /**
     * @param array<string,mixed> $config
     */
    protected function hydrate(array $config): void
    {
        static::hydrator()->hydrate($config, $this);
    }

    abstract protected static function hydrator(): HydratorInterface;
}
