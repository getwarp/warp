<?php

declare(strict_types=1);

namespace Warp\Collection\Operation;

/**
 * @template K of array-key
 * @template V
 * @extends AbstractOperation<K,V,K,V|null>
 */
final class FirstOperation extends AbstractOperation
{
    /**
     * @var callable(V,K,\Traversable<K,V>):bool
     */
    private $callback;

    /**
     * @param null|callable(V,K,\Traversable<K,V>):bool $callback
     * @param bool $preserveKeys
     */
    public function __construct(?callable $callback = null, bool $preserveKeys = false)
    {
        parent::__construct($preserveKeys);

        $this->callback = $callback ?? static fn ($v) => true;
    }

    protected function generator(\Traversable $iterator): \Generator
    {
        /**
         * @var K $offset
         * @var V $value
         */
        foreach ($iterator as $offset => $value) {
            if (($this->callback)($value, $offset, $iterator)) {
                return yield $offset => $value;
            }
        }

        return yield null;
    }
}
