<?php

declare(strict_types=1);

namespace Warp\Bridge\Cycle;

use Cycle\ORM\ORMInterface;
use Cycle\ORM\Transaction;
use Cycle\ORM\TransactionInterface;
use Warp\DataSource\EntityPersisterInterface;

/**
 * @template E of object
 * @implements EntityPersisterInterface<E>
 */
final class CycleEntityPersister implements EntityPersisterInterface
{
    private ORMInterface $orm;

    private int $transactionMode;

    public function __construct(ORMInterface $orm, int $transactionMode = TransactionInterface::MODE_CASCADE)
    {
        $this->orm = $orm;
        $this->transactionMode = $transactionMode;
    }

    public function persistCascade(): void
    {
        $this->transactionMode = TransactionInterface::MODE_CASCADE;
    }

    public function persistSingle(): void
    {
        $this->transactionMode = TransactionInterface::MODE_ENTITY_ONLY;
    }

    /**
     * @inheritDoc
     * @throws \Throwable
     */
    public function save(object $entity, object ...$entities): void
    {
        $transaction = new Transaction($this->orm);

        foreach ([$entity, ...$entities] as $e) {
            $transaction->persist($e, $this->transactionMode);
        }

        $transaction->run();
    }

    /**
     * @inheritDoc
     * @throws \Throwable
     */
    public function remove(object $entity, object ...$entities): void
    {
        $transaction = new Transaction($this->orm);

        foreach ([$entity, ...$entities] as $e) {
            $transaction->delete($e, $this->transactionMode);
        }

        $transaction->run();
    }
}
