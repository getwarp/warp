<?php

declare(strict_types=1);

namespace Warp\Criteria;

use Webmozart\Expression\Expression;

interface CriteriaInterface
{
    /**
     * Returns criteria `where` expression.
     * @return Expression|null
     */
    public function getWhere(): ?Expression;

    /**
     * Replaces criteria `where` expression.
     * @param Expression|null $expression
     * @return static new criteria object.
     */
    public function where(?Expression $expression): self;

    /**
     * Joins provided expression with current one using conjunction.
     * @param Expression $expression
     * @return static new criteria object.
     */
    public function andWhere(Expression $expression): self;

    /**
     * Joins provided expression with current one using disjunction.
     * @param Expression $expression
     * @return static new criteria object.
     */
    public function orWhere(Expression $expression): self;

    /**
     * Returns criteria `orderBy` option.
     * @return array<string,int>
     */
    public function getOrderBy(): array;

    /**
     * Replaces criteria `orderBy` option.
     * @param array<string,int> $orderBy
     * @return static new criteria object.
     */
    public function orderBy(array $orderBy): self;

    /**
     * Returns criteria `offset` option.
     * @return int
     */
    public function getOffset(): int;

    /**
     * Replaces criteria `offset` option.
     * @param int|null $offset
     * @return static new criteria object.
     */
    public function offset(?int $offset): self;

    /**
     * Returns criteria `limit` option.
     * @return int|null
     */
    public function getLimit(): ?int;

    /**
     * Replaces criteria `limit` option.
     * @param int|null $limit
     * @return static new criteria object.
     */
    public function limit(?int $limit): self;

    /**
     * Returns criteria `include` option.
     * @return mixed[]
     */
    public function getInclude(): array;

    /**
     * Replaces criteria `include` option.
     * @param mixed[] $include
     * @return static new criteria object.
     */
    public function include(array $include): self;

    /**
     * Merges parameters from current criteria and given one.
     * @param CriteriaInterface $criteria
     * @return static new criteria object.
     */
    public function merge(self $criteria): self;
}
