<?php

declare(strict_types=1);

namespace spaceonfire\Bridge\Cycle\Migrator;

use Psr\Container\ContainerInterface;
use spaceonfire\Bridge\Cycle\Migrator\Exception\MigrationLockedException;
use Symfony\Component\Lock\LockFactory;
use Symfony\Component\Lock\LockInterface;

final class LockFacade
{
    private const LOCK_NAME = 'cycle/migrations';

    private ContainerInterface $container;

    private ?LockInterface $lock = null;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __destruct()
    {
        $this->release();
    }

    public function acquire(): void
    {
        // Lock already acquired by current process
        if (null !== $this->lock) {
            return;
        }

        if (!$this->container->has(LockFactory::class)) {
            return;
        }

        /** @var LockFactory $lockFactory */
        $lockFactory = $this->container->get(LockFactory::class);
        $lock = $lockFactory->createLock(self::LOCK_NAME);
        if (!$lock->acquire()) {
            throw new MigrationLockedException();
        }
        $this->lock = $lock;
    }

    public function release(): void
    {
        if (null === $this->lock) {
            return;
        }

        $this->lock->release();
        $this->lock = null;
    }
}
