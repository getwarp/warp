<?php

declare(strict_types=1);

namespace Warp\Collection\Iterator;

use Warp\Collection\OperationInterface;

/**
 * @template K of array-key
 * @template V
 * @implements \IteratorAggregate<K,V>
 */
final class OperationIterator implements \IteratorAggregate
{
    /**
     * @var \Traversable<array-key,mixed>
     */
    private \Traversable $iterator;

    /**
     * @var OperationInterface<array-key,mixed,K,V>
     */
    private OperationInterface $operation;

    /**
     * @param \Traversable<array-key,mixed> $iterator
     * @param OperationInterface<array-key,mixed,K,V> $operation
     */
    public function __construct(\Traversable $iterator, OperationInterface $operation)
    {
        $this->iterator = $iterator;
        $this->operation = $operation;
    }

    /**
     * @return \Generator<K,V>
     */
    public function getIterator(): \Generator
    {
        yield from $this->operation->apply($this->iterator);
    }
}
