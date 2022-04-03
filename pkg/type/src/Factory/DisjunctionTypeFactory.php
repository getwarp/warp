<?php

declare(strict_types=1);

namespace Warp\Type\Factory;

use Warp\Type\DisjunctionType;
use Warp\Type\Exception\TypeNotSupportedException;
use Warp\Type\TypeInterface;

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
