<?php

declare(strict_types=1);

namespace spaceonfire\Bridge\Cycle;

use Cycle\ORM\ORMInterface;
use Cycle\ORM\Transaction;
use Cycle\ORM\TransactionInterface;
use spaceonfire\DataSource\EntityPersisterInterface;

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
