<?php

declare(strict_types=1);

namespace spaceonfire\DataSource\Adapters\CycleOrm\Query;

use Cycle\ORM\Select;
use RuntimeException;
use spaceonfire\Collection\CollectionInterface;
use spaceonfire\Collection\TypedCollection;
use spaceonfire\Criteria\Adapter\SpiralPagination\PaginableCriteria;
use spaceonfire\Criteria\CriteriaInterface;
use spaceonfire\DataSource\EntityInterface;
use spaceonfire\DataSource\MapperInterface;
use spaceonfire\DataSource\QueryInterface;
use Spiral\Database\Query\SelectQuery;

class CycleQuery implements QueryInterface
{
    /**
     * @var Select
     */
    protected $select;
    /**
     * @var MapperInterface
     */
    protected $mapper;

    /**
     * CycleQuery constructor.
     * @param Select $select
     * @param MapperInterface $mapper
     */
    public function __construct(Select $select, MapperInterface $mapper)
    {
        $this->select = $select;
        $this->mapper = $mapper;
    }

    /**
     * @inheritDoc
     */
    public function limit(int $limit): QueryInterface
    {
        $this->select->limit($limit);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function offset(int $offset): QueryInterface
    {
        $this->select->offset($offset);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function fetchOne(): ?EntityInterface
    {
        $entity = $this->select->fetchOne();

        if ($entity === null) {
            return null;
        }

        if ($entity instanceof EntityInterface) {
            return $entity;
        }

        // @codeCoverageIgnoreStart
        throw new RuntimeException('Associated with repository class must implement ' . EntityInterface::class);
        // @codeCoverageIgnoreEnd
    }

    /**
     * @inheritDoc
     */
    public function fetchAll(): CollectionInterface
    {
        $items = $this->select->fetchAll();

        return new TypedCollection($items, EntityInterface::class);
    }

    /**
     * @inheritDoc
     */
    public function matching(CriteriaInterface $criteria): QueryInterface
    {
        if ($expression = $criteria->getWhere()) {
            $scope = (new CycleQueryExpressionVisitor($this->mapper))->dispatch($expression);
            $this->select->andWhere($scope);
        }

        foreach ($criteria->getOrderBy() as $key => $order) {
            $this->select->orderBy(
                $this->mapper->convertNameToStorage($key),
                $order === SORT_ASC ? SelectQuery::SORT_ASC : SelectQuery::SORT_DESC
            );
        }

        if ($criteria instanceof PaginableCriteria) {
            $criteria->getPaginator()->paginate($this);
        } else {
            if ($criteria->getOffset()) {
                $this->offset($criteria->getOffset());
            }

            if ($criteria->getLimit() !== null) {
                $this->limit($criteria->getLimit());
            }
        }

        // TODO: support load options
        $this->select->load($criteria->getInclude());

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function count(?string $column = null): int
    {
        return $this->select->count($column);
    }
}
