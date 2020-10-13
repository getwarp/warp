<?php

declare(strict_types=1);

namespace spaceonfire\Type\Factory;

use spaceonfire\Type\DisjunctionType;
use spaceonfire\Type\Exception\TypeNotSupportedException;
use spaceonfire\Type\Type;

final class DisjunctionTypeFactory implements TypeFactoryInterface
{
    use TypeFactoryTrait;

    /**
     * @inheritDoc
     */
    public function supports(string $type): bool
    {
        if ($this->parent === null) {
            return false;
        }

        $parts = explode(DisjunctionType::DELIMITER, $type);

        if (count($parts) < 2) {
            return false;
        }

        foreach ($parts as $part) {
            if (!$this->parent->supports(trim($part))) {
                return false;
            }
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function make(string $type): Type
    {
        if (!$this->supports($type)) {
            throw new TypeNotSupportedException($type, DisjunctionType::class);
        }

        $conjuncts = array_map(function (string $subType): Type {
            return $this->parent->make(trim($subType));
        }, explode(DisjunctionType::DELIMITER, $type));

        return new DisjunctionType($conjuncts);
    }
}
