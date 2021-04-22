<?php

declare(strict_types=1);

namespace spaceonfire\Collection\Operation;

/**
 * @template K of array-key
 * @template V
 * @extends AbstractOperation<K,V,K,V>
 */
final class ReverseOperation extends AbstractOperation
{
    protected function generator(\Traversable $iterator): \Generator
    {
        yield from \array_reverse(
            \iterator_to_array($iterator, $this->preserveKeys),
            $this->preserveKeys
        );
    }
}
