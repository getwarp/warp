<?php

declare(strict_types=1);

namespace spaceonfire\DataSource\Fixtures\Infrastructure\Persistence\User;

use Cycle\Schema\Definition\Entity;
use Cycle\Schema\Definition\Field;
use spaceonfire\Criteria\CriteriaInterface;
use spaceonfire\DataSource\Adapters\CycleOrm\Mapper\UuidCycleMapper;
use spaceonfire\DataSource\Adapters\CycleOrm\Repository\AbstractCycleRepository;
use spaceonfire\DataSource\Exceptions\NotFoundException;
use spaceonfire\DataSource\Fixtures\Domain\User\Exceptions;
use spaceonfire\DataSource\Fixtures\Domain\User\User;
use spaceonfire\DataSource\Fixtures\Domain\User\UserRepositoryInterface;

/**
 * Class CycleUserRepository
 * @package spaceonfire\DataSource\Fixtures\Infrastructure\Persistence\User
 * @codeCoverageIgnore
 */
class CycleUserRepository extends AbstractCycleRepository implements UserRepositoryInterface
{
    /**
     * @inheritDoc
     */
    public static function getTableName(): string
    {
        return 'users';
    }

    /**
     * @inheritDoc
     */
    public static function getEntityClass(): string
    {
        return User::class;
    }

    /**
     * @inheritDoc
     */
    protected static function defineInternal(): Entity
    {
        $e = new Entity();

        $e->setRole('user');

        $e->getFields()->set('id', (new Field())->setType('string(36)')->setColumn('id')->setPrimary(true));

        $e->getFields()->set('name', (new Field())->setType('string(255)')->setColumn('name'));

        $e->setMapper(UuidCycleMapper::class);

        return $e;
    }

    protected static function makeNotFoundException($primary): NotFoundException
    {
        return new Exceptions\UserNotFoundException();
    }

    /**
     * @inheritDoc
     */
    public function findByPrimary($primary): User
    {
        /** @var User $entity */
        $entity = parent::findByPrimary($primary);
        return $entity;
    }

    /**
     * @inheritDoc
     */
    public function findOne(?CriteriaInterface $criteria = null): ?User
    {
        return parent::findOne($criteria);
    }
}
