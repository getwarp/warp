<?php

declare(strict_types=1);

namespace spaceonfire\Bridge\Cycle\Select;

use Cycle\ORM\Select\QueryBuilder;
use spaceonfire\Common\Field\FieldInterface;
use spaceonfire\Criteria\Expression\ExpressionFactory;
use spaceonfire\DataSource\AbstractExpressionVisitor;
use spaceonfire\DataSource\ExpressionNotSupportedException;
use spaceonfire\DataSource\IdenticalPropertyExtractor;
use spaceonfire\DataSource\PropertyExtractorInterface;
use Spiral\Database\Injection\Fragment;
use Spiral\Database\Injection\Parameter;
use Webmozart\Expression\Constraint;
use Webmozart\Expression\Expression;
use Webmozart\Expression\Logic;

/**
 * @internal
 * @extends AbstractExpressionVisitor<QueryBuilder,QueryBuilder>
 */
final class CycleExpressionVisitor extends AbstractExpressionVisitor
{
    private PropertyExtractorInterface $propertyExtractor;

    public function __construct(
        ?PropertyExtractorInterface $propertyExtractor = null,
        ?ExpressionFactory $expressionFactory = null
    ) {
        parent::__construct($expressionFactory);

        $this->propertyExtractor = $propertyExtractor ?? new IdenticalPropertyExtractor();
    }

    public function visitConjunction(Logic\AndX $expression): callable
    {
        return function (QueryBuilder $queryBuilder) use ($expression) {
            foreach ($expression->getConjuncts() as $conjunct) {
                $queryBuilder->andWhere($this->dispatch($conjunct));
            }
            return $queryBuilder;
        };
    }

    public function visitDisjunction(Logic\OrX $expression): callable
    {
        return function (QueryBuilder $queryBuilder) use ($expression) {
            foreach ($expression->getDisjuncts() as $disjunct) {
                $queryBuilder->orWhere($this->dispatch($disjunct));
            }
            return $queryBuilder;
        };
    }

    public function visitAlwaysTrue(Logic\AlwaysTrue $expression): callable
    {
        return static fn (QueryBuilder $queryBuilder) => $queryBuilder->where(new Fragment('1 = 1'));
    }

    public function visitAlwaysFalse(Logic\AlwaysFalse $expression): callable
    {
        return static fn (QueryBuilder $queryBuilder) => $queryBuilder->where(new Fragment('1 = 0'));
    }

    public function visitComparison(FieldInterface $field, Expression $expression, bool $isNegated = false): callable
    {
        if ($isNegated) {
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

            /** @var string|Expression|null $expressionClass */
            foreach ($supportedNegateExpressions as $expressionClass) {
                if (null !== $expressionClass && $expression instanceof $expressionClass) {
                    $expression = $this->negateExpression($expression);
                    break;
                }
            }

            if (null === $expressionClass) {
                throw ExpressionNotSupportedException::new($expression);
            }
        }

        if ($expression instanceof Constraint\Equals || $expression instanceof Constraint\Same) {
            return function (QueryBuilder $queryBuilder) use ($field, $expression) {
                $val = $this->visitValue($field, $expression->getComparedValue());
                return $queryBuilder->where(
                    $this->formatField($field),
                    null === $val ? 'is' : '=',
                    new Parameter($val),
                );
            };
        }

        if ($expression instanceof Constraint\NotEquals || $expression instanceof Constraint\NotSame) {
            return function (QueryBuilder $queryBuilder) use ($field, $expression) {
                $val = $this->visitValue($field, $expression->getComparedValue());
                return $queryBuilder->where(
                    $this->formatField($field),
                    null === $val ? 'is not' : '<>',
                    new Parameter($val),
                );
            };
        }

        if ($expression instanceof Constraint\In) {
            return function (QueryBuilder $queryBuilder) use ($field, $expression, $isNegated) {
                $val = \array_map(fn ($v) => $this->visitValue($field, $v), $expression->getAcceptedValues());
                return $queryBuilder->where(
                    $this->formatField($field),
                    ($isNegated ? 'not ' : '') . 'in',
                    new Parameter($val),
                );
            };
        }

        if ($expression instanceof Constraint\Contains) {
            return function (QueryBuilder $queryBuilder) use ($field, $expression, $isNegated) {
                $val = $this->visitValue($field, $expression->getComparedValue());
                return $queryBuilder->where(
                    $this->formatField($field),
                    ($isNegated ? 'not ' : '') . 'like',
                    new Parameter('%' . $val . '%'),
                );
            };
        }

        if ($expression instanceof Constraint\StartsWith) {
            return function (QueryBuilder $queryBuilder) use ($field, $expression, $isNegated) {
                $val = $this->visitValue($field, $expression->getAcceptedPrefix());
                return $queryBuilder->where(
                    $this->formatField($field),
                    ($isNegated ? 'not ' : '') . 'like',
                    new Parameter($val . '%'),
                );
            };
        }

        if ($expression instanceof Constraint\EndsWith) {
            return function (QueryBuilder $queryBuilder) use ($field, $expression, $isNegated) {
                $val = $this->visitValue($field, $expression->getAcceptedSuffix());
                return $queryBuilder->where(
                    $this->formatField($field),
                    ($isNegated ? 'not ' : '') . 'like',
                    new Parameter('%' . $val),
                );
            };
        }

        if (
            $expression instanceof Constraint\GreaterThan ||
            $expression instanceof Constraint\GreaterThanEqual ||
            $expression instanceof Constraint\LessThan ||
            $expression instanceof Constraint\LessThanEqual
        ) {
            return function (QueryBuilder $queryBuilder) use ($field, $expression) {
                $val = $this->visitValue($field, $expression->getComparedValue());

                $operatorsMap = [
                    Constraint\GreaterThan::class => '>',
                    Constraint\GreaterThanEqual::class => '>=',
                    Constraint\LessThan::class => '<',
                    Constraint\LessThanEqual::class => '<=',
                ];

                /**
                 * @var class-string<Expression> $expressionClass
                 * @var string $operator
                 */
                foreach ($operatorsMap as $expressionClass => $operator) {
                    if ($expression instanceof $expressionClass) {
                        break;
                    }
                }

                return $queryBuilder->where($this->formatField($field), $operator, new Parameter($val));
            };
        }

        throw ExpressionNotSupportedException::new($expression);
    }

    /**
     * Format expression value for storage
     * @param FieldInterface $field
     * @param mixed $value
     * @return mixed
     */
    public function visitValue(FieldInterface $field, $value)
    {
        return $this->propertyExtractor->extractValue(\implode('.', $field->getElements()), $value);
    }

    private function formatField(FieldInterface $field): string
    {
        return $this->propertyExtractor->extractName(\implode('.', $field->getElements()));
    }
}
