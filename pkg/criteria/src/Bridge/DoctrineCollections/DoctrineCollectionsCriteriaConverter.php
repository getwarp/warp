<?php

declare(strict_types=1);

namespace spaceonfire\Criteria\Bridge\DoctrineCollections;

use Doctrine\Common\Collections\Criteria as DoctrineCriteria;
use spaceonfire\Criteria\Criteria;
use spaceonfire\Criteria\CriteriaInterface;

class DoctrineCollectionsCriteriaConverter
{
    /**
     * Converts Doctrine criteria to spaceonfire criteria
     * @param DoctrineCriteria $doctrineCriteria
     * @param string $comparisonMethod
     * @return CriteriaInterface
     */
    public function convert(
        DoctrineCriteria $doctrineCriteria,
        string $comparisonMethod = 'property'
    ): CriteriaInterface {
        $criteria = new Criteria();

        if (null !== $doctrineExpression = $doctrineCriteria->getWhereExpression()) {
            $expression = (new DoctrineCollectionsExpressionConverter($comparisonMethod))
                ->dispatch($doctrineExpression);

            $criteria->where($expression);
        }

        $orderBy = array_map(static function (string $ordering): int {
            return DoctrineCriteria::ASC === $ordering ? SORT_ASC : SORT_DESC;
        }, $doctrineCriteria->getOrderings());

        $criteria->orderBy($orderBy);

        if (null !== $offset = $doctrineCriteria->getFirstResult()) {
            $criteria->offset($offset);
        }

        if (null !== $limit = $doctrineCriteria->getMaxResults()) {
            $criteria->limit($limit);
        }

        return $criteria;
    }
}
