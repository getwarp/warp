<?php

declare(strict_types=1);

namespace spaceonfire\Bridge\Cycle\Select;

use Cycle\ORM\ORMInterface;
use Cycle\ORM\Select\QueryBuilder;
use Cycle\ORM\Select\ScopeInterface;
use spaceonfire\Bridge\Cycle\Mapper\CyclePropertyExtractor;
use spaceonfire\Criteria\CriteriaInterface;
use Spiral\Database\Query\SelectQuery;

final class CriteriaScope implements ScopeInterface
{
    private CriteriaInterface $criteria;

    private CyclePropertyExtractor $propertyExtractor;

    public function __construct(CriteriaInterface $criteria, ORMInterface $orm, string $role)
    {
        $this->criteria = $criteria;
        $this->propertyExtractor = new CyclePropertyExtractor($orm, $role);
    }

    public function apply(QueryBuilder $query): void
    {
        if (null !== $expression = $this->criteria->getWhere()) {
            $scope = (new CycleExpressionVisitor($this->propertyExtractor))->dispatch($expression);
            $query->andWhere($scope);
        }

        foreach ($this->criteria->getOrderBy() as $key => $order) {
            $query->orderBy(
                $this->propertyExtractor->extractName($key),
                \SORT_ASC === $order ? SelectQuery::SORT_ASC : SelectQuery::SORT_DESC
            );
        }

        $query->offset($this->criteria->getOffset());
        if (null !== $limit = $this->criteria->getLimit()) {
            $query->limit($limit);
        }

//        foreach ($this->criteria->getInclude() as $offset => $include) {
//            if (\is_string($include)) {
//                $query->load($include);
//            } else {
//                $query->load((string)$offset, (array)$include);
//            }
//        }
    }
}
