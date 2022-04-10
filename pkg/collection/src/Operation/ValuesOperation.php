<?php

declare(strict_types=1);

namespace Warp\Collection\Operation;

use Warp\Collection\OperationInterface;

/**
 * @template K of array-key
 * @template V
 * @implements OperationInterface<K,V,int,V>
 */
final class ValuesOperation implements OperationInterface
{
    /**
     * @inheritDoc
     * @return \Generator<int,V>
     */
    public function apply(\Traversable $iterator): \Generator
    {
        foreach ($iterator as $value) {
            yield $value;
        }
    }
}
