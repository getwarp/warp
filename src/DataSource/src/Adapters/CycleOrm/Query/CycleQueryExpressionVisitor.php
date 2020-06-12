<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

namespace spaceonfire\DataSource\Adapters\CycleOrm\Query;

use Cycle\ORM\Select\QueryBuilder;
use spaceonfire\DataSource\Query\AbstractExpressionVisitor;
use Spiral\Database\Injection\Parameter;
use Webmozart\Expression\Constraint;
use Webmozart\Expression\Expression;
use Webmozart\Expression\Logic;

class CycleQueryExpressionVisitor extends AbstractExpressionVisitor
{
    /**
     * Generates Cycle's Query Builder scope for conjunction expression
     * @param Logic\AndX $expression
     * @return callable
     */
    public function visitConjunction(Logic\AndX $expression): callable
    {
        return function (QueryBuilder $queryBuilder) use ($expression) {
            foreach ($expression->getConjuncts() as $conjunct) {
                $queryBuilder->andWhere($this->dispatch($conjunct));
            }
            return $queryBuilder;
        };
    }

    /**
     * Generates Cycle's Query Builder scope for disjunction expression
     * @param Logic\OrX $expression
     * @return callable
     */
    public function visitDisjunction(Logic\OrX $expression): callable
    {
        return function (QueryBuilder $queryBuilder) use ($expression) {
            foreach ($expression->getDisjuncts() as $disjunct) {
                $queryBuilder->orWhere($this->dispatch($disjunct));
            }
            return $queryBuilder;
        };
    }

    /**
     * Generates Cycle's Query Builder scope for comparison expression on given field
     * @param string $field
     * @param Expression $expression
     * @param bool $isNegated
     * @return callable
     */
    public function visitComparison(string $field, Expression $expression, bool $isNegated = false): callable
    {
        $supportedNegateExpressions = [
            Constraint\Contains::class,
            Constraint\EndsWith::class,
            Constraint\StartsWith::class,
            Constraint\In::class,
            Constraint\Equals::class,
            Constraint\Same::class,
            Constraint\GreaterThan::class,
            Constraint\GreaterThanEqual::class,
            Constraint\LessThan::class,
            Constraint\LessThanEqual::class,
            null,
        ];

        if ($isNegated) {
            /** @var string|Expression|null $expressionClass */
            foreach ($supportedNegateExpressions as $expressionClass) {
                if ($expressionClass !== null && $expression instanceof $expressionClass) {
                    $expression = $this->negateExpression($expression);
                    break;
                }
            }

            if ($expressionClass === null) {
                throw $this->makeNotSupportedExpression($expression);
            }
        }

        switch (true) {
            case $expression instanceof Constraint\Equals:
            case $expression instanceof Constraint\Same:
                return function (QueryBuilder $queryBuilder) use ($field, $expression) {
                    $val = $this->visitValue($field, $expression->getComparedValue());
                    return $queryBuilder->where($field, $val === null ? 'is' : '=', new Parameter($val));
                };
            // no break

            case $expression instanceof Constraint\NotEquals:
            case $expression instanceof Constraint\NotSame:
                return function (QueryBuilder $queryBuilder) use ($field, $expression) {
                    $val = $this->visitValue($field, $expression->getComparedValue());
                    return $queryBuilder->where($field, $val === null ? 'is not' : '<>', new Parameter($val));
                };
            // no break

            case $expression instanceof Constraint\In:
                return function (QueryBuilder $queryBuilder) use ($field, $expression, $isNegated) {
                    $val = array_map(function ($v) use ($field) {
                        return $this->visitValue($field, $v);
                    }, $expression->getAcceptedValues());

                    $operator = ($isNegated ? 'not ' : '') . 'in';

                    return $queryBuilder->where($field, $operator, new Parameter($val));
                };
            // no break

            case $expression instanceof Constraint\Contains:
                return function (QueryBuilder $queryBuilder) use ($field, $expression, $isNegated) {
                    $val = $this->visitValue($field, $expression->getComparedValue());

                    $operator = ($isNegated ? 'not ' : '') . 'like';

                    return $queryBuilder->where($field, $operator, new Parameter('%' . $val . '%'));
                };
            // no break

            case $expression instanceof Constraint\StartsWith:
                return function (QueryBuilder $queryBuilder) use ($field, $expression, $isNegated) {
                    $val = $this->visitValue($field, $expression->getAcceptedPrefix());

                    $operator = ($isNegated ? 'not ' : '') . 'like';

                    return $queryBuilder->where($field, $operator, new Parameter($val . '%'));
                };
            // no break

            case $expression instanceof Constraint\EndsWith:
                return function (QueryBuilder $queryBuilder) use ($field, $expression, $isNegated) {
                    $val = $this->visitValue($field, $expression->getAcceptedSuffix());

                    $operator = ($isNegated ? 'not ' : '') . 'like';

                    return $queryBuilder->where($field, $operator, new Parameter('%' . $val));
                };
            // no break

            case $expression instanceof Constraint\GreaterThan:
            case $expression instanceof Constraint\GreaterThanEqual:
            case $expression instanceof Constraint\LessThan:
            case $expression instanceof Constraint\LessThanEqual:
                return function (QueryBuilder $queryBuilder) use ($field, $expression) {
                    $val = $this->visitValue($field, $expression->getComparedValue());

                    $operatorsMap = [
                        Constraint\GreaterThan::class => '>',
                        Constraint\GreaterThanEqual::class => '>=',
                        Constraint\LessThan::class => '<',
                        Constraint\LessThanEqual::class => '<=',
                    ];

                    /**
                     * @var string|Expression $expressionClass
                     * @var string $operator
                     */
                    foreach ($operatorsMap as $expressionClass => $operator) {
                        if ($expression instanceof $expressionClass) {
                            break;
                        }
                    }

                    return $queryBuilder->where($field, $operator, new Parameter($val));
                };
            // no break
        }

        throw $this->makeNotSupportedExpression($expression);
    }
}
