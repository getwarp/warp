<?php

declare(strict_types=1);

namespace spaceonfire\DataSource\Criteria;

use Doctrine\Common\Collections\Expr\Expression;
use Spiral\Pagination\Paginator;
use Spiral\Pagination\PaginatorInterface;
use Webmozart\Assert\Assert;

/**
 * Class Criteria
 * @package spaceonfire\DataSource\Criteria
 *
 * @method self where(Expression $expression)
 * @method self andWhere(Expression $expression)
 * @method self orWhere(Expression $expression)
 * @method self orderBy(array $orderings)
 * @method self setFirstResult($firstResult)
 * @method self setMaxResults($maxResults)
 */
class Criteria extends \Doctrine\Common\Collections\Criteria
{
    /**
     * @var PaginatorInterface
     */
    protected $paginator;
    /**
     * @var string[]
     */
    protected $include = [];

    /**
     * @inheritDoc
     * @return self
     */
    public static function create(): self
    {
        return new self();
    }

    /**
     * Getter for `paginator` property
     * @return PaginatorInterface|null
     */
    public function getPaginator(): ?PaginatorInterface
    {
        if ($this->paginator === null && $this->getMaxResults() !== null) {
            $paginator = new Paginator($this->getMaxResults());

            if ($this->getFirstResult() !== null) {
                $page = (int)($this->getFirstResult() / $this->getMaxResults());
                $paginator = $paginator->withPage($page);
            }

            $this->paginator = $paginator;
        }

        return $this->paginator;
    }

    /**
     * Setter for `paginator` property
     * @param PaginatorInterface $paginator
     * @return self
     */
    public function setPaginator(PaginatorInterface $paginator): self
    {
        if ($paginator instanceof Paginator) {
            $this->setMaxResults($paginator->getLimit());
            $this->setFirstResult($paginator->getOffset());
        }

        $this->paginator = $paginator;
        return $this;
    }

    /**
     * Getter for `include` property
     * @return string[]
     */
    public function getInclude(): array
    {
        return $this->include;
    }

    /**
     * Setter for `include` property
     * @param string[] $include
     * @return static
     */
    public function setInclude(array $include): self
    {
        Assert::allStringNotEmpty($include);
        $this->include = $include;
        return $this;
    }

    /**
     * Merges params from other criteria to current one
     * @param Criteria $criteria
     * @return $this
     */
    public function merge(self $criteria): self
    {
        if (!empty($newOrderings = $criteria->getOrderings())) {
            $this->orderBy($newOrderings);
        }

        if ($criteria->getPaginator()) {
            $this->setPaginator($criteria->getPaginator());
        }

        if ($criteria->getWhereExpression()) {
            $this->andWhere($criteria->getWhereExpression());
        }

        if (!empty($newIncludes = $criteria->getInclude())) {
            $this->setInclude($newIncludes);
        }

        return $this;
    }
}
