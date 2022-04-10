<?php

declare(strict_types=1);

namespace Warp\Container;

use PhpOption\Option;
use PhpOption\Some;

/**
 * @deprecated use {@see Some} option directly.
 * @template T
 * @extends Option<T>
 */
final class RawValueHolder extends Option
{
    /**
     * @var Some<T>
     */
    private Some $op;

    /**
     * @param T $value
     */
    public function __construct($value)
    {
        $this->op = new Some($value);
    }

    /**
     * @return T
     * @deprecated use {@see get()}
     */
    public function getValue()
    {
        return $this->get();
    }

    #[\ReturnTypeWillChange]
    public function getIterator()
    {
        return $this->op->getIterator();
    }

    public function get()
    {
        return $this->op->get();
    }

    public function getOrElse($default)
    {
        return $this->op->getOrElse($default);
    }

    public function getOrCall($callable)
    {
        return $this->op->getOrCall($callable);
    }

    public function getOrThrow(\Exception $ex)
    {
        return $this->op->getOrThrow($ex);
    }

    public function isEmpty()
    {
        return $this->op->isEmpty();
    }

    public function isDefined()
    {
        return $this->op->isDefined();
    }

    public function orElse(Option $else)
    {
        return $this->op->orElse($else);
    }

    public function ifDefined($callable): void
    {
        $this->op->ifDefined($callable);
    }

    public function forAll($callable)
    {
        return $this->op->forAll($callable);
    }

    public function map($callable)
    {
        return $this->op->map($callable);
    }

    public function flatMap($callable)
    {
        return $this->op->flatMap($callable);
    }

    public function filter($callable)
    {
        return $this->op->filter($callable);
    }

    public function filterNot($callable)
    {
        return $this->op->filterNot($callable);
    }

    public function select($value)
    {
        return $this->op->select($value);
    }

    public function reject($value)
    {
        return $this->op->reject($value);
    }

    public function foldLeft($initialValue, $callable)
    {
        return $this->op->foldLeft($initialValue, $callable);
    }

    public function foldRight($initialValue, $callable)
    {
        return $this->op->foldRight($initialValue, $callable);
    }
}
