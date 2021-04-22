<?php

declare(strict_types=1);

namespace spaceonfire\Collection\Operation;

use spaceonfire\Type\TypeInterface;

/**
 * @template K of array-key
 * @template V
 * @extends AbstractOperation<K,V,K,V>
 */
final class TypeCheckOperation extends AbstractOperation
{
    private TypeInterface $valueType;

    public function __construct(TypeInterface $valueType, bool $preserveKeys = false)
    {
        parent::__construct($preserveKeys);

        $this->valueType = $valueType;
    }

    protected function generator(\Traversable $iterator): \Generator
    {
        foreach ($iterator as $offset => $value) {
            if (!$this->valueType->check($value)) {
                throw new \LogicException(\sprintf(
                    'Iterator accepts only elements of type %s. Got: %s.',
                    $this->valueType,
                    \get_debug_type($value),
                ));
            }

            yield $offset => $value;
        }
    }
}
