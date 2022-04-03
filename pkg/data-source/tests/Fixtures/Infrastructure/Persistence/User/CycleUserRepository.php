<?php

declare(strict_types=1);

namespace Warp\DataSource\Fixtures\Infrastructure\Persistence\User;

use Cycle\ORM\ORMInterface;
use Warp\Criteria\CriteriaInterface;
use Warp\DataSource\Bridge\CycleOrm\Repository\AbstractCycleRepositoryAdapter;
use Warp\DataSource\Exceptions\NotFoundException;
use Warp\DataSource\Fixtures\Domain\User\Exceptions;
use Warp\DataSource\Fixtures\Domain\User\User;
use Warp\DataSource\Fixtures\Domain\User\UserRepositoryInterface;

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
