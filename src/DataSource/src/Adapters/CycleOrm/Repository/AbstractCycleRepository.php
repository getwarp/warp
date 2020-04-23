<?php

declare(strict_types=1);

namespace spaceonfire\DataSource\Adapters\CycleOrm\Repository;

use Cycle\ORM;
use Cycle\Schema\Definition\Entity;
use spaceonfire\Collection\CollectionInterface;
use spaceonfire\Criteria\CriteriaInterface;
use spaceonfire\DataSource\Adapters\CycleOrm\Mapper\BasicCycleMapper;
use spaceonfire\DataSource\Adapters\CycleOrm\Query\CycleQuery;
use spaceonfire\DataSource\EntityInterface;
use spaceonfire\DataSource\Exceptions\NotFoundException;
use spaceonfire\DataSource\Exceptions\RemoveException;
use spaceonfire\DataSource\Exceptions\SaveException;
use spaceonfire\DataSource\MapperInterface;
use spaceonfire\DataSource\QueryInterface;
use spaceonfire\DataSource\RepositoryInterface;
use Throwable;
use Webmozart\Assert\Assert;

abstract class AbstractCycleRepository implements RepositoryInterface
{
    /**
     * @var ORM\RepositoryInterface|ORM\Select\Repository
     */
    private $repository;
    /**
     * @var ORM\ORMInterface
     */
    private $orm;
    /**
     * @var ORM\Transaction
     */
    protected $transaction;

    /**
     * @param ORM\RepositoryInterface $repository
     * @param ORM\ORMInterface $orm
     */
    public function __construct(ORM\RepositoryInterface $repository, ORM\ORMInterface $orm)
    {
        $this->repository = $repository;
        $this->orm = $orm;
        $this->transaction = new ORM\Transaction($orm);
    }

    /**
     * Returns entity table name
     * @return string
     * @see \Cycle\Schema\Registry::linkTable()
     */
    abstract public static function getTableName(): string;

    /**
     * Returns entity class name
     * @return string|EntityInterface
     */
    abstract public static function getEntityClass(): string;

    /**
     * Returns Cycle entity definition
     * @return Entity
     */
    abstract protected static function defineInternal(): Entity;

    /**
     * Returns Cycle entity definition
     * @return Entity
     */
    final public static function define(): Entity
    {
        $e = static::defineInternal();

        if (!$e->getClass()) {
            $e->setClass(static::getEntityClass());
        }

        if (!$e->getMapper()) {
            $e->setMapper(BasicCycleMapper::class); // @codeCoverageIgnore
        }

        return $e;
    }

    /**
     * Creates query
     */
    protected function query(): QueryInterface
    {
        return new CycleQuery($this->repository->select(), $this->getMapper());
    }

    /**
     * @inheritDoc
     * @param bool $cascade
     */
    public function save($entity, bool $cascade = true): void
    {
        static::assertEntity($entity);

        $this->transaction->persist(
            $entity,
            $cascade ? ORM\Transaction::MODE_CASCADE : ORM\Transaction::MODE_ENTITY_ONLY
        );

        try {
            $this->transaction->run();
        } catch (Throwable $e) {
            throw static::makeSaveException($e);
        }
    }

    /**
     * @inheritDoc
     * @param bool $cascade
     */
    public function remove($entity, bool $cascade = true): void
    {
        static::assertEntity($entity);

        $this->transaction->delete(
            $entity,
            $cascade ? ORM\Transaction::MODE_CASCADE : ORM\Transaction::MODE_ENTITY_ONLY
        );

        try {
            $this->transaction->run();
            // @codeCoverageIgnoreStart
        } catch (Throwable $e) {
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

        if ($entity === null) {
            throw static::makeNotFoundException($primary);
        }

        static::assertEntity($entity);

        return $entity;
    }

    /**
     * @inheritDoc
     */
    public function findAll(?CriteriaInterface $criteria = null): CollectionInterface
    {
        $query = $this->query();

        if ($criteria !== null) {
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

        if ($criteria !== null) {
            $query->matching($criteria);
        }

        $entity = $query->fetchOne();

        if ($entity === null) {
            return null;
        }

        static::assertEntity($entity);

        return $entity;
    }

    /**
     * @inheritDoc
     */
    public function count(?CriteriaInterface $criteria = null): int
    {
        $query = $this->query();

        if ($criteria !== null) {
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
        $mapper = $this->orm->getMapper(static::getEntityClass());
        return $mapper;
    }

    protected static function assertEntity(object $entity): void
    {
        foreach ([EntityInterface::class, static::getEntityClass()] as $class) {
            Assert::isInstanceOf($entity, $class, 'Associated with repository class must implement %2$s. Got: %s');
        }
    }

    /**
     * @param mixed $primary
     * @return NotFoundException
     * @codeCoverageIgnore
     */
    protected static function makeNotFoundException($primary): NotFoundException
    {
        return new NotFoundException();
    }

    /**
     * @param Throwable $e
     * @return RemoveException
     * @codeCoverageIgnore
     */
    protected static function makeRemoveException(Throwable $e): RemoveException
    {
        return new RemoveException('Remove Exception', 0, $e);
    }

    /**
     * @param Throwable $e
     * @return SaveException
     * @codeCoverageIgnore
     */
    protected static function makeSaveException(Throwable $e): SaveException
    {
        return new SaveException('Save Exception', 0, $e);
    }
}
