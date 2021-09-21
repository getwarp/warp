<?php

declare(strict_types=1);

namespace spaceonfire\DataSource\Fixtures\Infrastructure\Persistence\User;

use Cycle\ORM\ORMInterface;
use spaceonfire\Criteria\CriteriaInterface;
use spaceonfire\DataSource\Bridge\CycleOrm\Repository\AbstractCycleRepositoryAdapter;
use spaceonfire\DataSource\Exceptions\NotFoundException;
use spaceonfire\DataSource\Fixtures\Domain\User\Exceptions;
use spaceonfire\DataSource\Fixtures\Domain\User\User;
use spaceonfire\DataSource\Fixtures\Domain\User\UserRepositoryInterface;

/**
 * @codeCoverageIgnore
 */
class CycleUserRepository extends AbstractCycleRepositoryAdapter implements UserRepositoryInterface
{
    public function __construct(ORMInterface $orm)
    {
        parent::__construct('user', $orm);
    }

    /**
     * @inheritDoc
     */
    public function findByPrimary($primary): User
    {
        return parent::findByPrimary($primary);
    }

    /**
     * @inheritDoc
     */
    public function findOne(?CriteriaInterface $criteria = null): ?User
    {
        return parent::findOne($criteria);
    }

    protected static function makeNotFoundException($primary = null): NotFoundException
    {
        return new Exceptions\UserNotFoundException(null, compact('primary'));
    }
}
