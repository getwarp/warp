<?php

declare(strict_types=1);

namespace Warp\Criteria;

use Warp\Common\Factory\StaticConstructorInterface;
use Warp\Criteria\Expression\ExpressionFactory;
use Webmozart\Expression\Expression;
use Webmozart\Expression\Logic\AndX;
use Webmozart\Expression\Logic\OrX;

final class Criteria implements CriteriaInterface, StaticConstructorInterface
{
    private ?Expression $expression = null;

    /**
     * @var array<string,int>
     */
    private array $orderBy = [];

    private ?int $offset = null;

    private ?int $limit = null;

    /**
     * @var mixed[]
     */
    private array $include = [];

    private function __construct()
    {
    }

    /**
     * @param Expression|null $where
     * @param array<string,int> $orderBy
     * @param int|null $offset
     * @param int|null $limit
     * @param mixed[] $include
     * @return static
     */
    public static function new(
        ?Expression $where = null,
        array $orderBy = [],
        ?int $offset = null,
        ?int $limit = null,
        array $include = []
    ): self {
        return (new self())
            ->where($where)
            ->orderBy($orderBy)
            ->offset($offset)
            ->limit($limit)
            ->include($include);
    }

    public function getWhere(): ?Expression
    {
        return $this->expression;
    }

    public function where(?Expression $expression): CriteriaInterface
    {
        $clone = clone $this;
        $clone->expression = $expression;
        return $clone;
    }

    public function andWhere(Expression $expression): CriteriaInterface
    {
        if (null === $this->expression) {
            return $this->where($expression);
        }

        return $this->where(new AndX([$this->expression, $expression]));
    }

    public function orWhere(Expression $expression): CriteriaInterface
    {
        if (null === $this->expression) {
            return $this->where($expression);
        }

        return $this->where(new OrX([$this->expression, $expression]));
    }

    public function getOrderBy(): array
    {
        return $this->orderBy;
    }

    public function orderBy(array $orderBy): CriteriaInterface
    {
        foreach ($orderBy as $offset => $direction) {
            if (!\is_string($offset) || (\SORT_ASC !== $direction && \SORT_DESC !== $direction)) {
                throw new \InvalidArgumentException(
                    'Argument #1 ($orderBy) must be an array where string keys mapped to SORT_ASC or SORT_DESC constants.'
                );
            }
        }

        $clone = clone $this;
        $clone->orderBy = $orderBy;
        return $clone;
    }

    public function getOffset(): int
    {
        return $this->offset ?? 0;
    }

    public function offset(?int $offset): CriteriaInterface
    {
        $clone = clone $this;
        $clone->offset = $offset;
        return $clone;
    }

    public function getLimit(): ?int
    {
        return $this->limit;
    }

    public function limit(?int $limit): CriteriaInterface
    {
        $clone = clone $this;
        $clone->limit = $limit;
        return $clone;
    }

    public function getInclude(): array
    {
        return $this->include;
    }

    public function include(array $include): CriteriaInterface
    {
        $clone = clone $this;
        $clone->include = $include;
        return $clone;
    }

    public function merge(CriteriaInterface $criteria): CriteriaInterface
    {
        $clone = clone $this;

        if (!empty($criteria->getOrderBy())) {
            $clone = $clone->orderBy($criteria->getOrderBy());
        }

        if (!empty($criteria->getInclude())) {
            $clone = $clone->include($criteria->getInclude());
        }

        if (null !== $criteria->getLimit()) {
            $clone = $clone->limit($criteria->getLimit());
            $clone = $clone->offset($criteria->getOffset());
        } elseif (0 < $criteria->getOffset()) {
            $clone = $clone->offset($criteria->getOffset());
        }

        if (null !== $criteria->getWhere()) {
            $clone = $clone->where($criteria->getWhere());
        }

        return $clone;
    }

    /**
     * @deprecated use {@see ExpressionFactory::new()} instead.
     */
    public static function expr(): ExpressionFactory
    {
        return ExpressionFactory::new();
    }
}
