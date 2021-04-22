<?php

declare(strict_types=1);

namespace spaceonfire\Type\Cast;

/**
 * @implements \IteratorAggregate<CasterInterface>
 */
final class CasterAggregate implements CasterInterface, \IteratorAggregate
{
    /**
     * @var CasterInterface[]
     */
    private array $casters;

    public function __construct(CasterInterface ...$casters)
    {
        $this->casters = $casters;
    }

    public function accepts($value): bool
    {
        foreach ($this->casters as $caster) {
            if ($caster->accepts($value)) {
                return true;
            }
        }

        return false;
    }

    public function cast($value)
    {
        foreach ($this->casters as $caster) {
            if ($caster->accepts($value)) {
                return $caster->cast($value);
            }
        }

        throw new \InvalidArgumentException(\sprintf(
            'Given value (%s) cannot be casted.',
            \get_debug_type($value),
        ));
    }

    /**
     * @return \Traversable<CasterInterface>
     */
    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->casters);
    }
}
