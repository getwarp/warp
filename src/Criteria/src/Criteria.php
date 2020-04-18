<?php

declare(strict_types=1);

namespace spaceonfire\Criteria;

use Webmozart\Assert\Assert;
use Webmozart\Expression\Expression;

class Criteria
{
    /**
     * @var Expression|null
     */
    private $expression;
    /**
     * @var array<string,int>
     */
    private $orderBy = [];
    /**
     * @var int|null
     */
    private $offset;
    /**
     * @var int|null
     */
    private $limit;

    /**
     * Getter for `expression` property
     * @return Expression|null
     */
    public function getExpression(): ?Expression
    {
        return $this->expression;
    }

    /**
     * Setter for `expression` property
     * @param Expression|null $expression
     */
    public function setExpression(?Expression $expression): void
    {
        $this->expression = $expression;
    }

    /**
     * Getter for `orderBy` property
     * @return array<string,int>
     */
    public function getOrderBy(): array
    {
        return $this->orderBy;
    }

    /**
     * Setter for `orderBy` property
     * @param array<string,int> $orderBy
     */
    public function setOrderBy(array $orderBy): void
    {
        $invalidMessage = 'Argument $orderBy must be an array where string keys mapped to `SORT_ASC` or `SORT_DESC` constants.';
        Assert::allString(array_keys($orderBy), $invalidMessage);
        Assert::allOneOf($orderBy, [SORT_ASC, SORT_DESC], $invalidMessage);
        $this->orderBy = $orderBy;
    }

    /**
     * Getter for `offset` property
     * @return int|null
     */
    public function getOffset(): ?int
    {
        return $this->offset;
    }

    /**
     * Setter for `offset` property
     * @param int|null $offset
     */
    public function setOffset(?int $offset): void
    {
        $this->offset = $offset;
    }

    /**
     * Getter for `limit` property
     * @return int|null
     */
    public function getLimit(): ?int
    {
        return $this->limit;
    }

    /**
     * Setter for `limit` property
     * @param int|null $limit
     */
    public function setLimit(?int $limit): void
    {
        $this->limit = $limit;
    }
}
