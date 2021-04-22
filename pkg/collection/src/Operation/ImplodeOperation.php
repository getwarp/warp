<?php

declare(strict_types=1);

namespace spaceonfire\Collection\Operation;

use spaceonfire\Collection\AlterValueTypeOperationInterface;
use spaceonfire\Common\Field\FieldInterface;

/**
 * @template K of array-key
 * @template V
 * @implements AlterValueTypeOperationInterface<K,V,int,string>
 */
final class ImplodeOperation implements AlterValueTypeOperationInterface
{
    private ?string $glue;

    private ?FieldInterface $field;

    public function __construct(?string $glue = null, ?FieldInterface $field = null)
    {
        $this->glue = $glue;
        $this->field = $field;
    }

    /**
     * @param \Traversable<K,V> $iterator
     * @return \Generator<int,string>
     */
    public function apply(\Traversable $iterator): \Generator
    {
        return yield from (new ReduceOperation($this->getCallback(), ''))->apply($iterator);
    }

    private function getCallback(): callable
    {
        $i = -1;

        return function ($accum, $element) use (&$i) {
            $value = null !== $this->field ? $this->field->extract($element) : $element;

            if (0 === ++$i) {
                return '' . $value;
            }

            return $accum . $this->glue . $value;
        };
    }
}
