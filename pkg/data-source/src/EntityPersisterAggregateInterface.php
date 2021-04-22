<?php

declare(strict_types=1);

namespace spaceonfire\DataSource;

interface EntityPersisterAggregateInterface
{
    /**
     * @template E of object
     * @param class-string<E> $entity
     * @return EntityPersisterInterface<E>
     */
    public function getPersister(string $entity): EntityPersisterInterface;
}
