<?php

declare(strict_types=1);

namespace Warp\Collection\Operation;

use Warp\Collection\AlterValueTypeOperationInterface;

/**
 * @template K of array-key
 * @template V
 * @implements AlterValueTypeOperationInterface<K,V,int,K>
 */
final class KeysOperation implements AlterValueTypeOperationInterface
{
    /**
     * @inheritDoc
     * @return \Generator<int,K>
     */
    public function apply(\Traversable $iterator): \Generator
    {
        foreach ($iterator as $offset => $_) {
            yield $offset;
        }
    }
}
