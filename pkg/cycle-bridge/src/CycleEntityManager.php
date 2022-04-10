<?php

declare(strict_types=1);

namespace Warp\Bridge\Cycle;

use Cycle\ORM\ORMInterface;
use Cycle\ORM\TransactionInterface;
use Warp\DataSource\EntityNotFoundExceptionFactoryInterface;
use Warp\DataSource\EntityPersisterAggregateInterface;
use Warp\DataSource\EntityPersisterInterface;
use Warp\DataSource\EntityReaderAggregateInterface;
use Warp\DataSource\EntityReaderInterface;

/**
 * @template E of object
 * @implements EntityPersisterInterface<E>
 */
final class CycleEntityManager implements
    EntityReaderAggregateInterface,
    EntityPersisterAggregateInterface,
    EntityPersisterInterface
{
    private ORMInterface $orm;

    private int $transactionMode;

    private ?EntityNotFoundExceptionFactoryInterface $notFoundExceptionFactory;

    public function __construct(
        ORMInterface $orm,
        int $transactionMode = TransactionInterface::MODE_CASCADE,
        ?EntityNotFoundExceptionFactoryInterface $notFoundExceptionFactory = null
    ) {
        $this->orm = $orm;
        $this->transactionMode = $transactionMode;
        $this->notFoundExceptionFactory = $notFoundExceptionFactory;
    }

    public function getReader(string $entity): EntityReaderInterface
    {
        return new CycleEntityReader($this->orm, $entity, $this->notFoundExceptionFactory);
    }

    public function getPersister(?string $entity = null, ?int $transactionMode = null): EntityPersisterInterface
    {
        return new CycleEntityPersister($this->orm, $transactionMode ?? $this->transactionMode);
    }

    public function save(object $entity, object ...$entities): void
    {
        $this->getPersister()->save($entity, ...$entities);
    }

    public function remove(object $entity, object ...$entities): void
    {
        $this->getPersister()->remove($entity, ...$entities);
    }
}
