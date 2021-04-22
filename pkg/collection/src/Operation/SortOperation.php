<?php

declare(strict_types=1);

namespace spaceonfire\Collection\Operation;

use spaceonfire\Common\Field\FieldInterface;

/**
 * @template K of array-key
 * @template V
 * @extends AbstractOperation<K,V,K,V>
 */
final class SortOperation extends AbstractOperation
{
    private int $direction;

    private ?FieldInterface $field;

    public function __construct(int $direction = \SORT_ASC, ?FieldInterface $field = null, bool $preserveKeys = false)
    {
        parent::__construct($preserveKeys);

        if (\SORT_ASC !== $direction && \SORT_DESC !== $direction) {
            throw new \InvalidArgumentException('Expected $direction to be either SORT_ASC or SORT_DESC.');
        }

        $this->direction = $direction;
        $this->field = $field;
    }

    protected function generator(\Traversable $iterator): \Generator
    {
        $array = \iterator_to_array($iterator, $this->preserveKeys);

        \uasort($array, function ($left, $right) {
            $lValue = $this->extractValue($left);
            $rValue = $this->extractValue($right);

            return ($lValue <=> $rValue) * (\SORT_DESC === $this->direction ? -1 : 1);
        });

        yield from $array;
    }

    /**
     * @param V $value
     * @return mixed|null
     */
    private function extractValue($value)
    {
        return null === $this->field ? $value : $this->field->extract($value);
    }
}
