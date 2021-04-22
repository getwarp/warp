<?php

declare(strict_types=1);

namespace spaceonfire\Collection\Operation;

use spaceonfire\Collection\AlterValueTypeOperationInterface;

/**
 * @template K of array-key
 * @template V
 * @template R
 * @extends AbstractOperation<K,V,int,R|null>
 * @implements AlterValueTypeOperationInterface<K,V,int,R|null>
 */
final class ReduceOperation extends AbstractOperation implements AlterValueTypeOperationInterface
{
    /**
     * @var callable(R|null,V):R
     */
    private $callback;

    /**
     * @var R|null
     */
    private $initialValue;

    /**
     * @param callable(R|null,V):R $callback
     * @param R|null $initialValue
     */
    public function __construct(callable $callback, $initialValue = null)
    {
        parent::__construct();

        $this->callback = $callback;
        $this->initialValue = $initialValue;
    }

    protected function generator(\Traversable $iterator): \Generator
    {
        $output = $this->initialValue;

        /** @var V $value */
        foreach ($iterator as $value) {
            $output = ($this->callback)($output, $value);
        }

        return yield $output;
    }
}
