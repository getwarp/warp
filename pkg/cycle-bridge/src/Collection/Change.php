<?php

declare(strict_types=1);

namespace spaceonfire\Bridge\Cycle\Collection;

/**
 * @template T of object
 * @template P
 */
final class Change
{
    public const ADD = 'ADD';

    public const REMOVE = 'REMOVE';

    /**
     * @phpstan-var self::ADD|self::REMOVE
     */
    private string $type;

    /**
     * @var T
     */
    private object $element;

    /**
     * @var P|null
     */
    private $pivot;

    /**
     * @phpstan-param self::ADD|self::REMOVE $type
     * @param T $element
     * @param P|null $pivot
     */
    private function __construct(string $type, object $element, $pivot = null)
    {
        $this->type = $type;
        $this->element = $element;
        $this->pivot = $pivot;
    }

    /**
     * @phpstan-return self::ADD|self::REMOVE
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return T
     */
    public function getElement(): object
    {
        return $this->element;
    }

    /**
     * @return P|null
     */
    public function getPivot()
    {
        return $this->pivot;
    }

    /**
     * @param P|null $pivot
     */
    public function setPivot($pivot): void
    {
        $this->pivot = $pivot;
    }

    /**
     * @param T $element
     * @param P|null $pivot
     * @return self<T,P>
     */
    public static function add(object $element, $pivot = null): self
    {
        return new self(self::ADD, $element, $pivot);
    }

    /**
     * @param T $element
     * @param P|null $pivot
     * @return self<T,P>
     */
    public static function remove(object $element, $pivot = null): self
    {
        return new self(self::REMOVE, $element, $pivot);
    }

    /**
     * @param T $element
     * @param T ...$elements
     * @return \Generator<self<T,mixed>>
     */
    public static function addElements(object $element, object ...$elements): \Generator
    {
        foreach ([$element, ...$elements] as $item) {
            yield self::add($item);
        }
    }

    /**
     * @param T $element
     * @param T ...$elements
     * @return \Generator<self<T,mixed>>
     */
    public static function removeElements(object $element, object ...$elements): \Generator
    {
        foreach ([$element, ...$elements] as $item) {
            yield self::remove($item);
        }
    }
}
