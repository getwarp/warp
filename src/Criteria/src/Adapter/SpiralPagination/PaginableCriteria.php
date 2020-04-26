<?php

declare(strict_types=1);

namespace spaceonfire\Criteria\Adapter\SpiralPagination;

use spaceonfire\Criteria\AbstractCriteriaAdapter;
use spaceonfire\Criteria\Criteria;
use spaceonfire\Criteria\CriteriaInterface;
use Spiral\Pagination\PaginableInterface;
use Spiral\Pagination\Paginator;
use Spiral\Pagination\PaginatorInterface;
use Webmozart\Assert\Assert;

class PaginableCriteria extends AbstractCriteriaAdapter implements PaginableInterface
{
    /**
     * @var PaginatorInterface|Paginator|null
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

        if ($paginator !== null) {
            $this->paginator = $paginator;
            $paginator->paginate($this);
        } else {
            $this->resetPaginator();
        }
    }

    /**
     * Getter for `paginator` property
     * @return PaginatorInterface|Paginator
     */
    public function getPaginator(): PaginatorInterface
    {
        Assert::notNull($this->paginator);
        return $this->paginator;
    }

    /**
     * @return PaginatorInterface|Paginator
     */
    private function makePaginator(): PaginatorInterface
    {
        $paginator = $this->paginator ?? new Paginator();

        Assert::isInstanceOf($paginator, Paginator::class);

        $limit = $this->getLimit() ?? 25;
        $tmpCount = $limit + $this->getOffset();
        $page = (int)($this->getOffset() / $limit) + 1;

        return $paginator->withCount(max($tmpCount, $paginator->count()))->withLimit($limit)->withPage($page);
    }

    private function resetPaginator(): void
    {
        $this->paginator = $this->makePaginator();
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
     * Clone criteria
     */
    public function __clone()
    {
        if ($this->paginator !== null) {
            $this->paginator = clone $this->paginator;
        }
    }
}
