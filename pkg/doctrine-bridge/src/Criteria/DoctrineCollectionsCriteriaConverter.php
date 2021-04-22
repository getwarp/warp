<?php

declare(strict_types=1);

namespace spaceonfire\Bridge\Doctrine\Criteria;

use Doctrine\Common\Collections\Criteria as DoctrineCriteria;
use spaceonfire\Criteria\Criteria;
use spaceonfire\Criteria\CriteriaInterface;

final class DoctrineCollectionsCriteriaConverter
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
        $criteria = Criteria::new();

        if (null !== $doctrineExpression = $doctrineCriteria->getWhereExpression()) {
            $expression = (new DoctrineCollectionsExpressionConverter($comparisonMethod))
                ->dispatch($doctrineExpression);

            $criteria = $criteria->where($expression);
        }

        $orderBy = \array_map(
            static fn (string $ordering): int => DoctrineCriteria::ASC === $ordering ? \SORT_ASC : \SORT_DESC,
            $doctrineCriteria->getOrderings()
        );

        $criteria = $criteria->orderBy($orderBy);

        if (null !== $offset = $doctrineCriteria->getFirstResult()) {
            $criteria = $criteria->offset($offset);
        }

        if (null !== $limit = $doctrineCriteria->getMaxResults()) {
            $criteria = $criteria->limit($limit);
        }

        return $criteria;
    }
}
