<?php

declare(strict_types=1);

namespace Warp\Collection\Operation;

use Warp\Collection\OperationInterface;
use Warp\Common\Field\FieldFactoryAggregate;
use Warp\Common\Field\FieldFactoryInterface;
use Warp\Criteria\CriteriaInterface;
use Warp\Criteria\FilterableInterface;

/**
 * @template K of array-key
 * @template V
 * @implements OperationInterface<K,V,K,V>
 */
final class MatchingOperation implements OperationInterface
{
    private CriteriaInterface $criteria;

    private bool $preserveKeys;

    private FieldFactoryInterface $fieldFactory;

    public function __construct(
        CriteriaInterface $criteria,
        ?FieldFactoryInterface $fieldFactory = null,
        bool $preserveKeys = false
    ) {
        $this->criteria = $criteria;
        $this->fieldFactory = $fieldFactory ?? FieldFactoryAggregate::default();
        $this->preserveKeys = $preserveKeys;
    }

    public function apply(\Traversable $iterator): \Traversable
    {
        if ($iterator instanceof FilterableInterface) {
            $output = $iterator->matching($this->criteria);

            if (!$output instanceof \Traversable) {
                throw new \LogicException(\sprintf(
                    'Expected %s::matching() to return Traversable. Got: %s.',
                    \get_class($iterator),
                    \get_debug_type($output),
                ));
            }

            return $output;
        }

        if (null !== $where = $this->criteria->getWhere()) {
            $iterator = (new FilterOperation(static fn ($value) => $where->evaluate($value), $this->preserveKeys))
                ->apply($iterator);
        }

        if ([] !== $orderBy = $this->criteria->getOrderBy()) {
            foreach ($orderBy as $key => $direction) {
                $iterator = (new SortOperation($direction, $this->fieldFactory->make($key), $this->preserveKeys))
                    ->apply($iterator);
            }
        }

        return (new SliceOperation($this->criteria->getOffset(), $this->criteria->getLimit(), $this->preserveKeys))
            ->apply($iterator);
    }
}
