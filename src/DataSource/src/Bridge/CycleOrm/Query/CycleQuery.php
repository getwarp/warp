<?php

declare(strict_types=1);

namespace spaceonfire\DataSource\Bridge\CycleOrm\Query;

use Cycle\ORM\Select;
use RuntimeException;
use spaceonfire\Collection\CollectionInterface;
use spaceonfire\Collection\TypedCollection;
use spaceonfire\Criteria\Bridge\SpiralPagination\PaginableCriteria;
use spaceonfire\Criteria\Criteria;
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
     * @var CriteriaInterface|null
     */
    protected $criteria;

    /**
     * @var int|null
     */
    protected $limit;

    /**
     * @var int|null
     */
    protected $offset;

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
    public function limit(?int $limit): QueryInterface
    {
        $this->limit = $limit;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function offset(?int $offset): QueryInterface
    {
        $this->offset = $offset;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function matching(CriteriaInterface $criteria): QueryInterface
    {
        $this->criteria = $criteria;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function fetchOne(): ?EntityInterface
    {
        $entity = $this->makeSelect()->fetchOne();

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
        $items = $this->makeSelect()->fetchAll();

        return new TypedCollection($items, EntityInterface::class);
    }

    /**
     * @inheritDoc
     */
    public function count(?string $column = null): int
    {
        $select = clone $this->select;

        if (null !== $this->criteria) {
            $criteria = clone $this->criteria;
            $criteria->limit(null);
            $criteria->offset(null);

            $select = $this->applyCriteria($select, $criteria);
        }

        return $select->count($column);
    }

    private function makeSelect(): Select
    {
        $select = clone $this->select;

        $criteria = null === $this->criteria ? new Criteria() : clone $this->criteria;
        $criteria->limit($this->limit ?? $criteria->getLimit());
        $criteria->offset($this->offset ?? $criteria->getOffset());

        return $this->applyCriteria($select, $criteria);
    }

    private function applyCriteria(Select $select, CriteriaInterface $criteria): Select
    {
        if ($expression = $criteria->getWhere()) {
            $scope = (new CycleQueryExpressionVisitor($this->mapper))->dispatch($expression);
            $select->andWhere($scope);
        }

        foreach ($criteria->getOrderBy() as $key => $order) {
            $select->orderBy(
                $this->mapper->convertNameToStorage($key),
                $order === SORT_ASC ? SelectQuery::SORT_ASC : SelectQuery::SORT_DESC
            );
        }

        if ($criteria instanceof PaginableCriteria) {
            $criteria->paginate($select);
        } else {
            if ($criteria->getOffset()) {
                $select->offset($criteria->getOffset());
            }

            if ($criteria->getLimit() !== null) {
                $select->limit($criteria->getLimit());
            }
        }

        // TODO: support load options
        $select->load($criteria->getInclude());

        return $select;
    }
}
