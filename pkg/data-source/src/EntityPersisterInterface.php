<?php

declare(strict_types=1);

namespace spaceonfire\DataSource;

/**
 * @template E of object
 */
interface EntityPersisterInterface
{
    /**
     * Persist given entities to storage.
     * @param E $entity
     * @param E ...$entities
     */
    public function save(object $entity, object ...$entities): void;

    /**
     * Remove given entities from storage.
     * @param E $entity
     * @param E ...$entities
     */
    public function remove(object $entity, object ...$entities): void;
}
