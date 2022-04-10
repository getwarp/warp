<?php

declare(strict_types=1);

namespace Warp\Collection\Operation;

use Warp\Collection\AlterValueTypeOperationInterface;
use Warp\Collection\Iterator\ArrayCacheIterator;
use Warp\Common\Field\FieldInterface;

/**
 * @template K of array-key
 * @template V
 * @implements AlterValueTypeOperationInterface<K,V,int,int|float|null>
 */
final class AverageOperation implements AlterValueTypeOperationInterface
{
    private ?FieldInterface $field;

    public function __construct(?FieldInterface $field = null)
    {
        $this->field = $field;
    }

    /**
     * @param \Traversable<K,V> $iterator
     * @return \Generator<int,int|float|null>
     */
    public function apply(\Traversable $iterator): \Generator
    {
        if (!$iterator instanceof \Countable) {
            $iterator = ArrayCacheIterator::wrap($iterator);
        }

        $count = $iterator->count();

        if (0 === $count) {
            return yield null;
        }

        $sum = (new SumOperation($this->field))->apply($iterator)->current();

        return yield $sum / $count;
    }
}
