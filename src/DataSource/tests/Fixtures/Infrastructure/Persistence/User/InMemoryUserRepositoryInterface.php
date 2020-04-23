<?php

declare(strict_types=1);

namespace spaceonfire\DataSource\Fixtures\Infrastructure\Persistence\User;

use spaceonfire\Collection\Collection;
use spaceonfire\Collection\CollectionInterface;
use spaceonfire\Criteria\CriteriaInterface;
use spaceonfire\DataSource\EntityInterface;
use spaceonfire\DataSource\Fixtures\Domain\User\Exceptions\UserNotFoundException;
use spaceonfire\DataSource\Fixtures\Domain\User\User;
use spaceonfire\DataSource\Fixtures\Domain\User\UserRepositoryInterface;
use spaceonfire\DataSource\Fixtures\Infrastructure\Mapper\StubMapper;
use spaceonfire\DataSource\MapperInterface;
use Webmozart\Assert\Assert;

/**
 * Class InMemoryUserRepositoryInterface
 * @package spaceonfire\DataSource\Fixtures\Infrastructure\Persistence\User
 * @codeCoverageIgnore
 */
class InMemoryUserRepositoryInterface implements UserRepositoryInterface
{
    /**
     * @var User[]
     */
    private $storage;
    /**
     * @var MapperInterface
     */
    private $mapper;

    /**
     * InMemoryUserRepository constructor.
     * @param User[] $users
     */
    public function __construct(array $users)
    {
        $this->storage = (new Collection($users))->indexBy('id')->all();

        $this->mapper = new StubMapper();
    }

    /**
     * @inheritDoc
     */
    public function getMapper(): MapperInterface
    {
        return $this->mapper;
    }

    /**
     * @inheritDoc
     */
    public function save($entity): void
    {
        Assert::isInstanceOf($entity, User::class);
        $this->storage[$entity['id']] = $entity;
    }

    /**
     * @inheritDoc
     */
    public function remove($entity): void
    {
        Assert::isInstanceOf($entity, User::class);
        unset($this->storage[$entity['id']]);
    }

    /**
     * @inheritDoc
     */
    public function findByPrimary($primary): User
    {
        Assert::uuid($primary);

        if (isset($this->storage[$primary])) {
            return $this->storage[$primary];
        }

        throw new UserNotFoundException();
    }

    /**
     * @inheritDoc
     */
    public function findAll(?CriteriaInterface $criteria = null): CollectionInterface
    {
        return new Collection(array_values($this->storage));
    }

    /**
     * @inheritDoc
     */
    public function findOne(?CriteriaInterface $criteria = null): ?User
    {
        return $this->findAll($criteria)->first();
    }

    /**
     * @inheritDoc
     */
    public function count(?CriteriaInterface $criteria = null): int
    {
        return count($this->findAll($criteria));
    }

    /**
     * @param mixed $id
     * @return mixed|EntityInterface
     * @deprecated
     * @codeCoverageIgnore
     */
    public function getById($id)
    {
        return $this->findByPrimary($id);
    }

    /**
     * @param mixed $criteria
     * @return CollectionInterface|User[]
     * @deprecated
     * @codeCoverageIgnore
     */
    public function getList($criteria)
    {
        return $this->findAll($criteria);
    }
}
