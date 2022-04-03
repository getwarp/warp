<?php

declare(strict_types=1);

namespace Warp\Type\Factory;

use Warp\Type\ConjunctionType;
use Warp\Type\Exception\TypeNotSupportedException;
use Warp\Type\TypeInterface;

final class ConjunctionTypeFactory extends AbstractAggregatedTypeFactory
{
    public function __construct()
    {
        parent::__construct(ConjunctionType::DELIMITER);
    }

    /**
     * @inheritDoc
     */
    public function make(string $type): TypeInterface
    {
        $parsed = $this->parse($type);

        if (null === $parsed || null === $this->parent) {
            throw new TypeNotSupportedException($type, ConjunctionType::class);
        }

        return new ConjunctionType(array_map([$this->parent, 'make'], $parsed));
    }
}
