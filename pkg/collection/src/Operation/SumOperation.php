<?php

declare(strict_types=1);

namespace spaceonfire\Collection\Operation;

use spaceonfire\Collection\AlterValueTypeOperationInterface;
use spaceonfire\Common\Field\FieldInterface;

/**
 * @template K of array-key
 * @template V
 * @implements AlterValueTypeOperationInterface<K,V,int,int|float>
 */
final class SumOperation implements AlterValueTypeOperationInterface
{
    private ?FieldInterface $field;

    public function __construct(?FieldInterface $field = null)
    {
        $this->field = $field;
    }

    /**
     * @param \Traversable<K,V> $iterator
     * @return \Generator<int,int|float>
     */
    public function apply(\Traversable $iterator): \Generator
    {
        return yield from (new ReduceOperation($this->getCallback(), 0))->apply($iterator);
    }

    private function getCallback(): callable
    {
        return function ($accum, $element) {
            $value = null !== $this->field ? $this->field->extract($element) : $element;

            if (!\is_numeric($value)) {
                throw new \LogicException(\sprintf('Non-numeric value used in %s.', self::class));
            }

            return $accum + $value;
        };
    }
}
