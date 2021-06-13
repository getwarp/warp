<?php

declare(strict_types=1);

namespace spaceonfire\Type\Factory;

use spaceonfire\Type\ConjunctionType;
use spaceonfire\Type\Exception\TypeNotSupportedException;
use spaceonfire\Type\TypeInterface;

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
