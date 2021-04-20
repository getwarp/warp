<?php

declare(strict_types=1);

namespace spaceonfire\Criteria;

use spaceonfire\Criteria\Expression\ExpressionFactory;
use Webmozart\Assert\Assert;
use Webmozart\Expression\Expression;

class Criteria implements CriteriaInterface
{
    /**
     * @var Expression|null
     */
    protected $expression;

    /**
     * @var array<string,int>
     */
    protected $orderBy = [];

    /**
     * @var int|null
     */
    protected $offset;

    /**
     * @var int|null
     */
    protected $limit;

    /**
     * @var mixed[]
     */
    protected $include = [];

    /**
     * @var ExpressionFactory
     */
    private static $expressionFactory;

    /**
     * Criteria constructor.
     * @param Expression|null $where
     * @param array<string,int> $orderBy
     * @param int|null $offset
     * @param int|null $limit
     * @param mixed[] $include
     */
    public function __construct(
        ?Expression $where = null,
        array $orderBy = [],
        ?int $offset = null,
        ?int $limit = null,
        array $include = []
    ) {
        $this
            ->where($where)
            ->orderBy($orderBy)
            ->offset($offset)
            ->limit($limit)
            ->include($include);
    }

    /**
     * @inheritDoc
     */
    public function getWhere(): ?Expression
    {
        return $this->expression;
    }

    /**
     * @inheritDoc
     */
    public function where(?Expression $expression): CriteriaInterface
    {
        $this->expression = $expression;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function andWhere(Expression $expression): CriteriaInterface
    {
        if (null === $this->expression) {
            return $this->where($expression);
        }

        return $this->where(self::expr()->andX([$this->expression, $expression]));
    }

    /**
     * @inheritDoc
     */
    public function orWhere(Expression $expression): CriteriaInterface
    {
        if (null === $this->expression) {
            return $this->where($expression);
        }

        return $this->where(self::expr()->orX([$this->expression, $expression]));
    }

    /**
     * @inheritDoc
     */
    public function getOrderBy(): array
    {
        return $this->orderBy;
    }

    /**
     * @inheritDoc
     */
    public function orderBy(array $orderBy): CriteriaInterface
    {
        $invalidMessage = 'Argument $orderBy must be an array where string keys mapped to `SORT_ASC` or `SORT_DESC` constants.';
        Assert::allString(array_keys($orderBy), $invalidMessage);
        Assert::allOneOf($orderBy, [SORT_ASC, SORT_DESC], $invalidMessage);
        $this->orderBy = $orderBy;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getOffset(): int
    {
        return $this->offset ?? 0;
    }

    /**
     * @inheritDoc
     */
    public function offset(?int $offset): CriteriaInterface
    {
        $this->offset = $offset;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getLimit(): ?int
    {
        return $this->limit;
    }

    /**
     * @inheritDoc
     */
    public function limit(?int $limit): CriteriaInterface
    {
        $this->limit = $limit;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getInclude(): array
    {
        return $this->include;
    }

    /**
     * @inheritDoc
     */
    public function include(array $include): CriteriaInterface
    {
        $this->include = $include;
        return $this;
    }

    /**
     * @inheritDoc
     * The original criteria will not be changed, a new one will be returned instead.
     */
    public function merge(CriteriaInterface $criteria): CriteriaInterface
    {
        $clone = clone $this;

        if (!empty($criteria->getOrderBy())) {
            $clone->orderBy($criteria->getOrderBy());
        }

        if (!empty($criteria->getInclude())) {
            $clone->include($criteria->getInclude());
        }

        if (null !== $criteria->getLimit()) {
            $clone->limit($criteria->getLimit());
            $clone->offset($criteria->getOffset());
        } elseif (0 < $criteria->getOffset()) {
            $clone->offset($criteria->getOffset());
        }

        if (null !== $criteria->getWhere()) {
            $clone->where($criteria->getWhere());
        }

        return $clone;
    }

    /**
     * @inheritDoc
     */
    public static function expr(): ExpressionFactory
    {
        if (null === self::$expressionFactory) {
            self::$expressionFactory = new ExpressionFactory();
        }

        return self::$expressionFactory;
    }
}
