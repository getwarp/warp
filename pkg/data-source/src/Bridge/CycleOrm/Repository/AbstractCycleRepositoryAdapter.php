<?php

declare(strict_types=1);

namespace Warp\DataSource\Bridge\CycleOrm\Repository;

use Cycle\ORM;
use Warp\Collection\CollectionInterface;
use Warp\Criteria\CriteriaInterface;
use Warp\DataSource\Bridge\CycleOrm\Query\CycleQuery;
use Warp\DataSource\EntityInterface;
use Warp\DataSource\Exceptions\NotFoundException;
use Warp\DataSource\Exceptions\RemoveException;
use Warp\DataSource\Exceptions\SaveException;
use Warp\DataSource\MapperInterface;
use Warp\DataSource\QueryInterface;
use Warp\DataSource\RepositoryInterface;
use Webmozart\Assert\Assert;

abstract class AbstractCycleRepositoryAdapter implements RepositoryInterface
{
    /**
     * @var string
     */
    protected $role;

    /**
     * @var ORM\RepositoryInterface|ORM\Select\Repository
     */
    protected $repository;

    /**
     * @var ORM\ORMInterface
     */
    protected $orm;

    /**
     * @var ORM\Transaction
     */
    protected $transaction;

    /**
     * @param string $role
     * @param ORM\ORMInterface $orm
     */
    public function __construct(string $role, ORM\ORMInterface $orm)
    {
        $this->role = $role;
        $this->orm = $orm;
        $this->repository = $orm->getRepository($role);
        $this->transaction = new ORM\Transaction($orm);
    }

    /**
     * @inheritDoc
     * @param bool $cascade
     */
    public function save($entity, bool $cascade = true): void
    {
        $this->assertEntity($entity);

        $this->transaction->persist(
            $entity,
            $cascade ? ORM\Transaction::MODE_CASCADE : ORM\Transaction::MODE_ENTITY_ONLY
        );

        try {
            $this->transaction->run();
        } catch (\Throwable $e) {
            throw static::makeSaveException($e);
        }
    }

    /**
     * @inheritDoc
     * @param bool $cascade
     */
    public function remove($entity, bool $cascade = true): void
    {
        $this->assertEntity($entity);

        $this->transaction->delete(
            $entity,
            $cascade ? ORM\Transaction::MODE_CASCADE : ORM\Transaction::MODE_ENTITY_ONLY
        );

        try {
            $this->transaction->run();
            // @codeCoverageIgnoreStart
        } catch (\Throwable $e) {
            throw static::makeRemoveException($e);
            // @codeCoverageIgnoreEnd
        }
    }

    /**
     * @inheritDoc
     */
    public function findByPrimary($primary)
    {
        $entity = $this->repository->findByPK($primary);

        if (null === $entity) {
            throw static::makeNotFoundException($primary);
        }

        $this->assertEntity($entity);

        return $entity;
    }

    /**
     * @inheritDoc
     */
    public function findAll(?CriteriaInterface $criteria = null): CollectionInterface
    {
        $query = $this->query();

        if (null !== $criteria) {
            $query->matching($criteria);
        }

        return $query->fetchAll();
    }

    /**
     * @inheritDoc
     */
    public function findOne(?CriteriaInterface $criteria = null)
    {
        $query = $this->query();

        if (null !== $criteria) {
            $query->matching($criteria);
        }

        $entity = $query->fetchOne();

        if (null === $entity) {
            return null;
        }

        $this->assertEntity($entity);

        return $entity;
    }

    /**
     * @inheritDoc
     */
    public function count(?CriteriaInterface $criteria = null): int
    {
        $query = $this->query();

        if (null !== $criteria) {
            $query->matching($criteria);
        }

        return $query->count();
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
     * @return CollectionInterface|EntityInterface[]|mixed[]
     * @deprecated
     * @codeCoverageIgnore
     */
    public function getList($criteria)
    {
        return $this->findAll($criteria);
    }

    /**
     * @inheritDoc
     */
    public function getMapper(): MapperInterface
    {
        /** @var MapperInterface $mapper */
        $mapper = $this->orm->getMapper($this->role);
        Assert::isInstanceOf($mapper, MapperInterface::class);
        return $mapper;
    }

    protected function assertEntity(object $entity): void
    {
        $entityClass = $this->orm->getSchema()->define($this->role, ORM\Schema::ENTITY);

        if (null === $entityClass || ($entityClass === $this->role && !class_exists($entityClass))) {
            return;
        }

        $entityClasses = [
            EntityInterface::class,
            $entityClass,
        ];

        foreach ($entityClasses as $class) {
            Assert::isInstanceOf($entity, $class, 'Associated with repository class must implement %2$s. Got: %s');
        }
    }

    /**
     * @param mixed|null $primary
     * @return NotFoundException
     * @codeCoverageIgnore
     */
    protected static function makeNotFoundException($primary = null): NotFoundException
    {
        return new NotFoundException(null, compact('primary'));
    }

    /**
     * @param \Throwable $e
     * @return RemoveException
     * @codeCoverageIgnore
     */
    protected static function makeRemoveException(\Throwable $e): RemoveException
    {
        return new RemoveException(null, [], 0, $e);
    }

    /**
     * @param \Throwable $e
     * @return SaveException
     * @codeCoverageIgnore
     */
    protected static function makeSaveException(\Throwable $e): SaveException
    {
        return new SaveException(null, [], 0, $e);
    }

    /**
     * Creates query
     */
    protected function query(): QueryInterface
    {
        return new CycleQuery($this->repository->select(), $this->getMapper());
    }
}
