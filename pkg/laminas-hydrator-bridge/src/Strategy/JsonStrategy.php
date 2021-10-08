<?php

declare(strict_types=1);

namespace spaceonfire\Bridge\LaminasHydrator\Strategy;

use Laminas\Hydrator\Strategy\StrategyInterface;

final class JsonStrategy implements StrategyInterface
{
    private bool $associative;

    private int $decodeFlags;

    private int $encodeFlags;

    private int $depth;

    public function __construct(
        bool $associative = true,
        int $decodeFlags = 0,
        int $encodeFlags = 0,
        int $depth = 512
    ) {
        $this->associative = $associative;
        $this->decodeFlags = $decodeFlags | \JSON_THROW_ON_ERROR;
        $this->encodeFlags = ($encodeFlags & ~\JSON_PARTIAL_OUTPUT_ON_ERROR) | \JSON_THROW_ON_ERROR;
        $this->depth = $depth;
    }

    public function extract($value, ?object $object = null): string
    {
        $json = \json_encode($value, $this->encodeFlags, $this->depth);
        \assert(false !== $json);
        return $json;
    }

    /**
     * @param mixed $value
     * @param array<string,mixed>|null $data
     * @return mixed
     */
    public function hydrate($value, ?array $data = null)
    {
        return \json_decode($value, $this->associative, $this->depth, $this->decodeFlags);
    }
}
