<?php

declare(strict_types=1);

namespace spaceonfire\Bridge\Cycle\Select;

use Cycle\ORM\Select\QueryBuilder;
use Cycle\ORM\Select\ScopeInterface;
use Spiral\Database\Query\SelectQuery;

final class ReferenceScope implements ScopeInterface
{
    /**
     * @var array<string,mixed>
     */
    private array $scope;

    /**
     * @var array<string,mixed>
     */
    private array $where;

    /**
     * @var array<string,SelectQuery::SORT_*>
     */
    private array $orderBy;

    /**
     * @param array<string,mixed> $scope
     * @param array<string,mixed>|null $where
     * @param array<string,SelectQuery::SORT_*> $orderBy
     */
    public function __construct(array $scope, ?array $where = null, array $orderBy = [])
    {
        $this->scope = $scope;
        $this->where = $where ?? $scope;
        $this->orderBy = $orderBy;
    }

    /**
     * @return array<string,mixed>
     */
    public function getScope(): array
    {
        return $this->scope;
    }

    public function apply(QueryBuilder $query): void
    {
        if ([] !== $this->where) {
            $query->andWhere($this->where);
        }
        if ([] !== $this->orderBy) {
            $query->orderBy($this->orderBy);
        }
    }
}
