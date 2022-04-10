<?php

declare(strict_types=1);

namespace Warp\Collection\Operation;

/**
 * @template K of array-key
 * @template V
 * @extends AbstractOperation<K,V,K,V>
 */
final class FilterOperation extends AbstractOperation
{
    /**
     * @var callable(V,K):bool
     */
    private $callback;

    /**
     * @param null|callable(V,K):bool $callback
     * @param bool $preserveKeys
     */
    public function __construct(?callable $callback = null, bool $preserveKeys = false)
    {
        parent::__construct($preserveKeys);

        $this->callback = $callback ?? static fn ($v) => (bool)$v;
    }

    protected function generator(\Traversable $iterator): \Generator
    {
        /**
         * @var K $offset
         * @var V $value
         */
        foreach ($iterator as $offset => $value) {
            if (($this->callback)($value, $offset)) {
                yield $offset => $value;
            }
        }
    }
}
