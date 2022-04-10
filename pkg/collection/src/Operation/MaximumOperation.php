<?php

declare(strict_types=1);

namespace Warp\Collection\Operation;

use Warp\Collection\AlterValueTypeOperationInterface;
use Warp\Common\Field\FieldInterface;

/**
 * @template K of array-key
 * @template V
 * @implements AlterValueTypeOperationInterface<K,V,int,int|float|null>
 */
final class MaximumOperation implements AlterValueTypeOperationInterface
{
    private ?FieldInterface $field;

    public function __construct(?FieldInterface $field = null)
    {
        $this->field = $field;
    }

    /**
     * @param \Traversable<K,V> $iterator
     * @return \Generator<int,int|float|null>
     */
    public function apply(\Traversable $iterator): \Generator
    {
        return yield from (new ReduceOperation($this->getCallback()))->apply($iterator);
    }

    private function getCallback(): callable
    {
        return function ($accum, $item) {
            $value = null === $this->field ? $item : $this->field->extract($item);
            $value ??= 0;

            if (!\is_numeric($value)) {
                throw new \LogicException(\sprintf('Non-numeric value used in %s.', self::class));
            }

            if (null === $accum) {
                return $value;
            }

            return $value > $accum ? $value : $accum;
        };
    }
}
