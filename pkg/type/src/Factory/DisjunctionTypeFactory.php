<?php

declare(strict_types=1);

namespace spaceonfire\Type\Factory;

use spaceonfire\Type\DisjunctionType;
use spaceonfire\Type\Exception\TypeNotSupportedException;
use spaceonfire\Type\TypeInterface;

final class DisjunctionTypeFactory extends AbstractAggregatedTypeFactory
{
    public function __construct()
    {
        parent::__construct(DisjunctionType::DELIMITER);
    }

    /**
     * @inheritDoc
     */
    public function make(string $type): TypeInterface
    {
        $parsed = $this->parse($type);

        if (null === $parsed || null === $this->parent) {
            throw new TypeNotSupportedException($type, DisjunctionType::class);
        }

        return new DisjunctionType(array_map([$this->parent, 'make'], $parsed));
    }
}
