<?php

declare(strict_types=1);

namespace spaceonfire\Bridge\Cycle\Select;

use Cycle\Database\Query\SelectQuery;
use Cycle\ORM\ORMInterface;
use Cycle\ORM\Select;
use Cycle\ORM\Select\AbstractLoader;
use Cycle\ORM\Select\QueryBuilder;
use Cycle\ORM\Select\ScopeInterface;
use spaceonfire\Bridge\Cycle\Mapper\CyclePropertyExtractor;
use spaceonfire\Criteria\CriteriaInterface;

final class CriteriaScope implements ScopeInterface, PrepareSelectScopeInterface, PrepareLoaderScopeInterface
{
    private CriteriaInterface $criteria;

    private ?ORMInterface $orm;

    public function __construct(CriteriaInterface $criteria, ?ORMInterface $orm = null)
    {
        $this->criteria = $criteria;
        $this->orm = $orm;
    }

    public function prepareSelect(Select $select): void
    {
        $select->load($this->criteria->getInclude());
    }

    public function prepareLoader(AbstractLoader $loader): void
    {
        foreach ($this->criteria->getInclude() as $offset => $include) {
            if (\is_string($include)) {
                $loader->loadRelation($include, [], false, true);
            } else {
                $loader->loadRelation((string)$offset, (array)$include, false, true);
            }
        }
    }

    public function apply(QueryBuilder $query): void
    {
        $propertyExtractor = $this->makePropertyExtractor($query->getLoader());

        if (null !== $expression = $this->criteria->getWhere()) {
            $scope = (new CycleExpressionVisitor($propertyExtractor))->dispatch($expression);
            $query->andWhere($scope);
        }

        foreach ($this->criteria->getOrderBy() as $key => $order) {
            $query->orderBy(
                $propertyExtractor->extractName($key),
                \SORT_ASC === $order ? SelectQuery::SORT_ASC : SelectQuery::SORT_DESC
            );
        }

        $query->offset($this->criteria->getOffset());
        if (null !== $limit = $this->criteria->getLimit()) {
            $query->limit($limit);
        }
    }

    private function makePropertyExtractor(AbstractLoader $loader): CyclePropertyExtractor
    {
        return new CyclePropertyExtractor($this->getOrm($loader), $loader->getTarget());
    }

    private function getOrm(AbstractLoader $loader): ORMInterface
    {
        if (null !== $this->orm) {
            return $this->orm;
        }

        $extractor = \Closure::bind(static fn () => $loader->orm, null, AbstractLoader::class);
        return $this->orm = $extractor();
    }
}
