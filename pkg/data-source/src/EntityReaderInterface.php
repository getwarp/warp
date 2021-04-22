<?php

declare(strict_types=1);

namespace spaceonfire\DataSource;

use spaceonfire\Collection\CollectionInterface;
use spaceonfire\Criteria\CriteriaInterface;

/**
 * @template E of object
 */
interface EntityReaderInterface
{
    /**
     * Returns entity by its primary field.
     * @param mixed $primary
     * @param CriteriaInterface|null $criteria
     * @return E
     * @throws EntityNotFoundException
     */
    public function findByPrimary($primary, ?CriteriaInterface $criteria = null): object;

    /**
     * Returns entity collection matching given criteria.
     * @param CriteriaInterface|null $criteria
     * @return CollectionInterface<E>
     */
    public function findAll(?CriteriaInterface $criteria = null): CollectionInterface;

    /**
     * Returns first entity matching given criteria.
     * @param CriteriaInterface|null $criteria
     * @return E|null
     */
    public function findOne(?CriteriaInterface $criteria = null): ?object;

    /**
     * Counts entities matching given criteria.
     * @param CriteriaInterface|null $criteria
     * @return int
     */
    public function count(?CriteriaInterface $criteria = null): int;
}
