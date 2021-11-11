<?php

declare(strict_types=1);

namespace spaceonfire\DataSource\Fixtures;

use spaceonfire\Collection\Collection;
use spaceonfire\Collection\CollectionInterface;
use spaceonfire\Criteria\CriteriaInterface;
use spaceonfire\DataSource\EntityNotFoundException;
use spaceonfire\DataSource\EntityPersisterAggregateInterface;
use spaceonfire\DataSource\EntityPersisterInterface;
use spaceonfire\DataSource\EntityReaderAggregateInterface;
use spaceonfire\DataSource\EntityReaderInterface;

/**
 * @template E of object
 * @implements EntityPersisterInterface<E>
 */
final class InMemoryEntityManager implements EntityReaderAggregateInterface, EntityPersisterAggregateInterface, EntityPersisterInterface
{
    /**
     * @var array<class-string<E>,array<string,E>>
     */
    public array $storage = [];

    public function __construct(object ...$entities)
    {
        foreach ($entities as $entity) {
            $this->save($entity);
        }
    }

    public function save(object $entity, object ...$entities): void
    {
        foreach ([$entity, ...$entities] as $e) {
            $entityClass = \get_class($e);
            $this->storage[$entityClass] ??= [];
            $this->storage[$entityClass][$this->extractPrimary($e)] = $e;
        }
    }

    public function remove(object $entity, object ...$entities): void
    {
        foreach ([$entity, ...$entities] as $e) {
            $entityClass = \get_class($e);
            unset($this->storage[$entityClass][$this->extractPrimary($e)]);
        }
    }

    public function getPersister(?string $entity = null): EntityPersisterInterface
    {
        return $this;
    }

    public function getReader(string $entity): EntityReaderInterface
    {
        return new class($this, $entity) implements EntityReaderInterface {
            private InMemoryEntityManager $em;
            private string $entity;

            public function __construct(InMemoryEntityManager $em, string $entity)
            {
                $this->em = $em;
                $this->entity = $entity;
            }

            public function findByPrimary($primary, ?CriteriaInterface $criteria = null): object
            {
                $entity = $this->em->storage[$this->entity][(string)$primary];

                if (null === $entity) {
                    throw EntityNotFoundException::byPrimary($this->entity, $primary);
                }

                return $entity;
            }

            public function findAll(?CriteriaInterface $criteria = null): CollectionInterface
            {
                return $this->collection($criteria);
            }

            public function findOne(?CriteriaInterface $criteria = null): ?object
            {
                return $this->collection($criteria)->first();
            }

            public function count(?CriteriaInterface $criteria = null): int
            {
                return $this->collection($criteria)->count();
            }

            private function collection(?CriteriaInterface $criteria = null): Collection
            {
                $collection = Collection::new($this->em->storage[$this->entity]);

                if (null !== $criteria) {
                    $collection = $collection->matching($criteria);
                }

                return $collection;
            }
        };
    }

    private function extractPrimary(object $entity): string
    {
        if ($entity instanceof Post) {
            return $entity->getId();
        }

        if ($entity instanceof User) {
            return $entity->getId();
        }

        throw new \RuntimeException();
    }
}
