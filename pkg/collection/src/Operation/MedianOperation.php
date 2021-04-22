<?php

declare(strict_types=1);

namespace spaceonfire\Collection\Operation;

use spaceonfire\Collection\AlterValueTypeOperationInterface;
use spaceonfire\Common\Field\FieldInterface;

/**
 * @template K of array-key
 * @template V
 * @implements AlterValueTypeOperationInterface<K,V,int,int|float|null>
 */
final class MedianOperation implements AlterValueTypeOperationInterface
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
        return yield $this->calcMedian($iterator);
    }

    /**
     * @param \Traversable<K,V> $iterator
     * @return float|int|null
     */
    private function calcMedian(\Traversable $iterator)
    {
        /** @var array<float|int> $array */
        $array = [];

        foreach ($iterator as $element) {
            $value = null === $this->field ? $element : $this->field->extract($element);
            $value = \filter_var($value, \FILTER_VALIDATE_INT | \FILTER_VALIDATE_FLOAT);

            if (false === $value) {
                throw new \LogicException(\sprintf('Non-numeric value used in %s.', self::class));
            }

            $array[] = $value;
        }

        if ([] === $array) {
            return null;
        }

        \usort($array, static fn ($left, $right) => $left <=> $right);

        $count = \count($array);
        $middleIndex = (int)\floor(($count - 1) / 2);

        if ($count % 2) {
            return $array[$middleIndex];
        }

        return ($array[$middleIndex] + $array[$middleIndex + 1]) / 2;
    }
}
