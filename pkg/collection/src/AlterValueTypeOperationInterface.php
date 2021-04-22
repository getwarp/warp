<?php

declare(strict_types=1);

namespace spaceonfire\Collection;

/**
 * Marks operations that has different input and output values type
 * @template IK of array-key
 * @template IV
 * @template OK of array-key
 * @template OV
 * @extends OperationInterface<IK,IV,OK,OV>
 */
interface AlterValueTypeOperationInterface extends OperationInterface
{
}
