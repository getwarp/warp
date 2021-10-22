<?php

declare(strict_types=1);

namespace spaceonfire\Bridge\Cycle\Collection;

/**
 * @template T of object
 * @template P
 * @extends \Traversable<array-key,T>
 */
interface ObjectCollectionInterface extends \Traversable
{
    /**
     * Return true if element has pivot data associated (can be null).
     *
     * @param T $element
     * @return bool
     */
    public function hasPivot(object $element): bool;

    /**
     * Return pivot data associated with element or null.
     *
     * @param T $element
     * @return P|null
     */
    public function getPivot(object $element);

    /**
     * Associate pivot data with the element.
     *
     * @param T $element
     * @param P|null $pivot
     */
    public function setPivot(object $element, $pivot): void;

    /**
     * Get all associated pivot data.
     *
     * @return \SplObjectStorage<T,P|null>
     */
    public function getPivotContext(): \SplObjectStorage;
}
