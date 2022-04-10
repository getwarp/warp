<?php

declare(strict_types=1);

namespace Warp\Collection;

/**
 * Operation interface.
 *
 * @template IK of array-key
 * @template IV
 * @template OK of array-key
 * @template OV
 */
interface OperationInterface
{
    /**
     * @param \Traversable<IK,IV> $iterator
     * @return \Traversable<OK,OV>
     */
    public function apply(\Traversable $iterator): \Traversable;
}
