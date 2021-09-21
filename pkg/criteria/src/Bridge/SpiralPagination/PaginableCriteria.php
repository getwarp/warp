<?php

declare(strict_types=1);

namespace spaceonfire\Criteria\Bridge\SpiralPagination;

use spaceonfire\Criteria\AbstractCriteriaDecorator;
use spaceonfire\Criteria\Criteria;
use spaceonfire\Criteria\CriteriaInterface;
use Spiral\Pagination\PaginableInterface;
use Spiral\Pagination\Paginator;
use Spiral\Pagination\PaginatorInterface;

class PaginableCriteria extends AbstractCriteriaDecorator implements PaginableInterface, PaginatorInterface
{
    /**
     * @var PaginatorInterface|Paginator
     */
    private $paginator;

    /**
     * PaginableCriteria constructor.
     * @param CriteriaInterface|null $criteria original criteria to proxy
     * @param PaginatorInterface|null $paginator
     */
    public function __construct(?CriteriaInterface $criteria = null, ?PaginatorInterface $paginator = null)
    {
        parent::__construct($criteria ?? new Criteria());

        if (null === $paginator) {
            $this->resetPaginator();
        } else {
            $this->paginator = $paginator;
            $paginator->paginate($this);
        }
    }

    /**
     * Clone criteria.
     */
    public function __clone()
    {
        $this->paginator = clone $this->paginator;
    }

    /**
     * @inheritDoc
     */
    public function getLimit(): int
    {
        return parent::getLimit() ?? 25;
    }

    /**
     * @inheritDoc
     */
    public function limit(?int $limit): CriteriaInterface
    {
        parent::limit($limit);

        $this->resetPaginator();
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function offset(?int $offset): CriteriaInterface
    {
        parent::offset($offset);

        $this->resetPaginator();
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function paginate(PaginableInterface $target): PaginatorInterface
    {
        $this->paginator = $this->paginator->paginate($target);

        return $this;
    }

    /**
     * Getter for `paginator` property
     * @return PaginatorInterface|Paginator
     */
    public function getPaginator(): PaginatorInterface
    {
        return $this->paginator;
    }

    private function resetPaginator(): void
    {
        $this->paginator = (new Paginator())
            ->withLimit($this->getLimit())
            ->withPage((int)($this->getOffset() / $this->getLimit()) + 1)
            ->withCount($this->paginator instanceof \Countable ? $this->paginator->count() : 0);
    }
}
