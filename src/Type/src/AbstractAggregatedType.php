<?php

declare(strict_types=1);

namespace spaceonfire\Type;

use ArrayIterator;
use IteratorAggregate;
use Webmozart\Assert\Assert;

abstract class AbstractAggregatedType implements Type, IteratorAggregate
{
    /**
     * @var Type[]
     */
    protected $types;

    /**
     * @var string
     */
    protected $delimiter;

    public function __construct(iterable $types, string $delimiter)
    {
        $types = array_values($this->prepareTypes($types));

        Assert::allIsInstanceOf($types, Type::class);
        Assert::minCount($types, 2);

        $this->types = $types;
        $this->delimiter = $delimiter;
    }

    /**
     * @inheritDoc
     */
    public function __toString(): string
    {
        return implode($this->delimiter, $this->types);
    }

    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->types);
    }

    private function prepareTypes(iterable $types): array
    {
        $output = [];

        foreach ($types as $type) {
            if ($type instanceof static) {
                $output += $this->prepareTypes($type);
                continue;
            }

            $output[(string)$type] = $type;
        }

        return $output;
    }
}
