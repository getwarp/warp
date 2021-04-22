<?php

declare(strict_types=1);

namespace spaceonfire\Collection\Operation;

use spaceonfire\Collection\OperationInterface;

/**
 * @template IK of array-key
 * @template IV
 * @template OK of array-key
 * @template OV
 * @implements OperationInterface<IK,IV,OK,OV>
 */
abstract class AbstractOperation implements OperationInterface
{
    protected bool $preserveKeys;

    /**
     * @param bool $preserveKeys
     */
    public function __construct(bool $preserveKeys = false)
    {
        $this->preserveKeys = $preserveKeys;
    }

    /**
     * @inheritDoc
     * @return \Generator<OK,OV>
     */
    final public function apply(\Traversable $iterator): \Generator
    {
        foreach ($this->generator($iterator) as $offset => $value) {
            if ($this->preserveKeys) {
                yield $offset => $value;
                continue;
            }

            yield $value;
        }
    }

    /**
     * @param \Traversable<IK,IV> $iterator
     * @return \Generator<OK,OV>
     */
    abstract protected function generator(\Traversable $iterator): \Generator;
}
