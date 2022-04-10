<?php

declare(strict_types=1);

namespace Warp\Collection\Operation;

/**
 * @template K of array-key
 * @template V
 * @extends AbstractOperation<K,V,K,V>
 */
final class MergeOperation extends AbstractOperation
{
    /**
     * @var array<iterable<K,V>>
     */
    private array $others;

    /**
     * @param array<iterable<K,V>> $others
     * @param bool $preserveKeys
     */
    public function __construct(array $others, bool $preserveKeys = false)
    {
        parent::__construct($preserveKeys);

        $this->others = $others;
    }

    protected function generator(\Traversable $iterator): \Generator
    {
        foreach ($iterator as $offset => $item) {
            yield $offset => $item;
        }

        foreach ($this->others as $collection) {
            foreach ($collection as $offset => $item) {
                yield $offset => $item;
            }
        }
    }
}
