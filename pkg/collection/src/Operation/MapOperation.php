<?php

declare(strict_types=1);

namespace Warp\Collection\Operation;

use Warp\Collection\AlterValueTypeOperationInterface;

/**
 * @template K of array-key
 * @template V
 * @template M
 * @extends AbstractOperation<K,V,K,M>
 * @implements AlterValueTypeOperationInterface<K,V,K,M>
 */
final class MapOperation extends AbstractOperation implements AlterValueTypeOperationInterface
{
    /**
     * @var callable(V,K):M
     */
    private $callback;

    /**
     * @param callable(V,K):M $callback
     * @param bool $preserveKeys
     */
    public function __construct(callable $callback, bool $preserveKeys = false)
    {
        parent::__construct($preserveKeys);

        $this->callback = $callback;
    }

    protected function generator(\Traversable $iterator): \Generator
    {
        /**
         * @var K $offset
         * @var V $value
         */
        foreach ($iterator as $offset => $value) {
            yield $offset => ($this->callback)($value, $offset);
        }
    }
}
