<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

namespace spaceonfire\DataSource\Adapters\CycleOrm\Query;

use Cycle\ORM\Select\QueryBuilder;
use InvalidArgumentException;
use spaceonfire\Criteria\Expression\Selector;
use spaceonfire\DataSource\MapperInterface;
use Spiral\Database\Injection\Parameter;
use Webmozart\Expression\Constraint;
use Webmozart\Expression\Expression;
use Webmozart\Expression\Logic;
use Webmozart\Expression\Selector as SelectorNS;

class CycleQueryExpressionVisitor
{
    /**
     * @var MapperInterface
     */
    protected $mapper;

    /**
     * CycleQueryExpressionVisitor constructor.
     * @param MapperInterface $mapper
     */
    public function __construct(MapperInterface $mapper)
    {
        $this->mapper = $mapper;
    }

    /**
     * Generates Cycle's Query Builder scope for given expression
     * @param Expression $expression
     * @return callable
     */
    public function dispatch(Expression $expression): callable
    {
        $methodsMap = [
            Logic\AndX::class => 'visitConjunction',
            Logic\OrX::class => 'visitDisjunction',
            SelectorNS\Key::class => 'visitSelector',
            SelectorNS\Property::class => 'visitSelector',
            Selector::class => 'visitSelector',
            '' => null,
        ];

        $method = $methodsMap[get_class($expression)] ?? null;

        if (!$method) {
            foreach ($methodsMap as $class => $method) {
                if ($class !== null && $expression instanceof $class) {
                    break;
                }
            }
        }

        if ($method) {
            return $this->$method($expression);
        }

        throw $this->makeNotSupportedExpression($expression);
    }

    private function makeNotSupportedExpression(
        Expression $expression,
        ?string $message = null
    ): InvalidArgumentException {
        return new InvalidArgumentException(
            sprintf($message ?? 'Not supported expression class: "%s"', get_class($expression))
        );
    }

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
     * Generates Cycle's Query Builder scope for field selector expression
     * @param Selector|SelectorNS\Property|SelectorNS\Key|SelectorNS\Selector $selector
     * @return callable
     */
    public function visitSelector(SelectorNS\Selector $selector): callable
    {
        if (!$selector instanceof Selector) {
            $supportedSelectors = [
                SelectorNS\Property::class => 'makeFromProperty',
                SelectorNS\Key::class => 'makeFromKey',
            ];

            foreach ($supportedSelectors as $selectorClass => $method) {
                if ($selector instanceof $selectorClass) {
                    $selector = Selector::$method($selector);
                    break;
                }
            }
        }

        if (!$selector instanceof Selector) {
            throw $this->makeNotSupportedExpression($selector); // @codeCoverageIgnore
        }

        $expression = $selector->getExpression();
        $isNegated = false;

        if ($expression instanceof Logic\Not) {
            $expression = $expression->getNegatedExpression();
            $isNegated = true;
        }

        return $this->visitComparison(
            implode('.', $selector->getPropertyPath()->getElements()),
            $expression,
            $isNegated
        );
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

    /**
     * Format expression value for storage
     * @param string $field
     * @param mixed $value
     * @return mixed
     */
    public function visitValue(string $field, $value)
    {
        return $this->mapper->convertToStorage($field, $value);
    }

    private function negateExpression(Expression $expressionToNegate): Expression
    {
        if ($expressionToNegate instanceof Constraint\Equals) {
            return new Constraint\NotEquals($expressionToNegate->getComparedValue());
        }

        if ($expressionToNegate instanceof Constraint\Same) {
            return new Constraint\NotSame($expressionToNegate->getComparedValue());
        }

        if ($expressionToNegate instanceof Constraint\GreaterThan) {
            return new Constraint\LessThanEqual($expressionToNegate->getComparedValue());
        }

        if ($expressionToNegate instanceof Constraint\GreaterThanEqual) {
            return new Constraint\LessThan($expressionToNegate->getComparedValue());
        }

        if ($expressionToNegate instanceof Constraint\LessThan) {
            return new Constraint\GreaterThanEqual($expressionToNegate->getComparedValue());
        }

        if ($expressionToNegate instanceof Constraint\LessThanEqual) {
            return new Constraint\GreaterThan($expressionToNegate->getComparedValue());
        }

        return $expressionToNegate;
    }
}
