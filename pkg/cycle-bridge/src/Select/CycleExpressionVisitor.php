<?php

declare(strict_types=1);

namespace spaceonfire\Bridge\Cycle\Select;

use Cycle\Database\Injection\Fragment;
use Cycle\Database\Injection\Parameter;
use Cycle\ORM\Relation;
use Cycle\ORM\Select\QueryBuilder;
use Cycle\ORM\Select\ScopeInterface;
use spaceonfire\Bridge\Cycle\Mapper\CyclePropertyExtractor;
use spaceonfire\Common\Field\FieldInterface;
use spaceonfire\Criteria\Expression\ExpressionFactory;
use spaceonfire\DataSource\AbstractExpressionVisitor;
use spaceonfire\DataSource\ExpressionNotSupportedException;
use Webmozart\Expression\Constraint;
use Webmozart\Expression\Expression;
use Webmozart\Expression\Logic;

/**
 * @internal
 * @extends AbstractExpressionVisitor<QueryBuilder,QueryBuilder>
 */
final class CycleExpressionVisitor extends AbstractExpressionVisitor
{
    private CyclePropertyExtractor $propertyExtractor;

    public function __construct(
        CyclePropertyExtractor $propertyExtractor,
        ?ExpressionFactory $expressionFactory = null
    ) {
        parent::__construct($expressionFactory);

        $this->propertyExtractor = $propertyExtractor;
    }

    public function dispatch(Expression $expression): callable
    {
        if ($expression instanceof ScopeInterface) {
            return $this->visitScope($expression);
        }

        return parent::dispatch($expression);
    }

    public function visitScope(ScopeInterface $expression): callable
    {
        return static function (QueryBuilder $queryBuilder) use ($expression) {
            $out = $expression->apply($queryBuilder);
            return $out instanceof QueryBuilder ? $out : $queryBuilder;
        };
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
        return static fn (QueryBuilder $queryBuilder) => $queryBuilder;
    }

    public function visitAlwaysFalse(Logic\AlwaysFalse $expression): callable
    {
        return static fn (QueryBuilder $queryBuilder) => $queryBuilder->where(new Fragment('1 = 0'));
    }

    public function visitComparison(FieldInterface $field, Expression $expression, bool $isNegated = false): callable
    {
        if ($expression instanceof Constraint\In && 0 === \count($expression->getAcceptedValues())) {
            return $this->visitAlwaysFalse(new Logic\AlwaysFalse());
        }

        if (null !== $handler = $this->visitComparisonRelation($field, $expression, $isNegated)) {
            return $handler;
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

    private function visitComparisonRelation(
        FieldInterface $field,
        Expression $expression,
        bool $isNegated = false
    ): ?callable {
        $fieldWithRelation = \implode('.', $field->getElements());
        $relSchema = $this->propertyExtractor->getRelationSchemaIfExists($fieldWithRelation);

        if (null === $relSchema) {
            return null;
        }

        $fieldWithoutRelation = $field->getElements();
        \array_pop($fieldWithoutRelation);
        $fieldWithoutRelation = \implode('.', $fieldWithoutRelation);

        if (
            $expression instanceof Constraint\Equals ||
            $expression instanceof Constraint\NotEquals ||
            $expression instanceof Constraint\Same ||
            $expression instanceof Constraint\NotSame
        ) {
            $values = [$expression->getComparedValue()];
        } elseif ($expression instanceof Constraint\In) {
            $values = $expression->getAcceptedValues();
        } else {
            throw ExpressionNotSupportedException::new($expression);
        }

        $innerKey = $relSchema[Relation::SCHEMA][Relation::INNER_KEY];
        $outerKey = $relSchema[Relation::SCHEMA][Relation::OUTER_KEY];
        $morphKey = $relSchema[Relation::SCHEMA][Relation::MORPH_KEY] ?? null;

        \assert(\is_string($innerKey) && \is_string($outerKey) && (\is_string($morphKey) || null === $morphKey));

        $filterKeys = \array_map(function ($value) use (
            $fieldWithRelation,
            $relSchema,
            $innerKey,
            $outerKey,
            $morphKey
        ) {
            if (\is_object($value)) {
                $role = $this->propertyExtractor->getRole($value);

                if ($relSchema[Relation::TARGET] !== $role) {
                    throw new \RuntimeException(\sprintf(
                        'Expected entity object of relation role "%s". Got: "%s".',
                        $relSchema[Relation::TARGET],
                        $role,
                    ));
                }
            } elseif (null === $value) {
                $role = null;
            } else {
                throw new \RuntimeException(\sprintf(
                    'You can use only null or objects for relation filter %s.',
                    $fieldWithRelation,
                ));
            }

            switch ($relSchema[Relation::TYPE]) {
                case Relation::BELONGS_TO:
                case Relation::REFERS_TO:
                case Relation::HAS_ONE:
                case Relation::HAS_MANY:
                case Relation::MORPHED_HAS_ONE:
                case Relation::MORPHED_HAS_MANY:
                    return [
                        $innerKey => null === $value ? null : $this->propertyExtractor->fetchKey($outerKey, $value),
                    ];

                case Relation::MANY_TO_MANY:
                    return [
                        $outerKey => null === $value ? null : $this->propertyExtractor->fetchKey($outerKey, $value),
                    ];

                case Relation::BELONGS_TO_MORPHED:
                    \assert(\is_string($morphKey));

                    return [
                        $innerKey => null === $value ? null : $this->propertyExtractor->fetchKey($outerKey, $value),
                        $morphKey => $role,
                    ];

                default:
                    throw new \RuntimeException(\sprintf(
                        'Cannot map comparison filter for relation: %s (%s).',
                        $fieldWithRelation,
                        $relSchema[Relation::TYPE]
                    ));
            }
        }, $values);

        $replaceExpression = static function (Expression $expression, array $values) {
            if ($expression instanceof Constraint\Same) {
                return new Constraint\Same(\reset($values));
            }

            if ($expression instanceof Constraint\Equals) {
                return new Constraint\Equals(\reset($values));
            }

            if ($expression instanceof Constraint\NotSame) {
                return new Constraint\NotSame(\reset($values));
            }

            if ($expression instanceof Constraint\NotEquals) {
                return new Constraint\NotEquals(\reset($values));
            }

            if ($expression instanceof Constraint\In) {
                if (1 === \count($values)) {
                    return $expression->isStrict()
                        ? new Constraint\Same(\reset($values))
                        : new Constraint\Equals(\reset($values));
                }

                return new Constraint\In($values, $expression->isStrict());
            }

            throw ExpressionNotSupportedException::new($expression);
        };

        switch ($relSchema[Relation::TYPE]) {
            case Relation::BELONGS_TO:
            case Relation::REFERS_TO:
            case Relation::HAS_ONE:
            case Relation::HAS_MANY:
            case Relation::MORPHED_HAS_ONE:
            case Relation::MORPHED_HAS_MANY:
                return $this->dispatch($this->expressionFactory->selector(
                    \ltrim($fieldWithoutRelation . '.' . $innerKey, '.'),
                    self::negateExpression(
                        $replaceExpression($expression, \array_unique(\array_column($filterKeys, $innerKey))),
                        $isNegated,
                    ),
                ));

            case Relation::MANY_TO_MANY:
                return $this->dispatch($this->expressionFactory->selector(
                    $fieldWithRelation . '.' . $outerKey,
                    self::negateExpression(
                        $replaceExpression($expression, \array_unique(\array_column($filterKeys, $outerKey))),
                        $isNegated,
                    ),
                ));

            case Relation::BELONGS_TO_MORPHED:
                $isNegated = $isNegated
                    || $expression instanceof Constraint\NotEquals
                    || $expression instanceof Constraint\NotSame;

                $valueGroups = [];

                foreach ($values as $value) {
                    $role = $value[$morphKey] ?? 'null';
                    $innerValue = $value[$innerKey];

                    $valueGroups[$role] ??= [
                        $morphKey => $value[$morphKey],
                        $innerKey => [],
                    ];
                    $valueGroups[$role][$innerKey][] = $innerValue;
                }

                $disjuncts = \array_map(function ($group) use ($fieldWithoutRelation, $innerKey, $morphKey) {
                    return $this->expressionFactory->andX([
                        $this->expressionFactory->selector(
                            \ltrim($fieldWithoutRelation . '.' . $morphKey, '.'),
                            $this->expressionFactory->same($group[$morphKey])
                        ),
                        $this->expressionFactory->selector(
                            \ltrim($fieldWithoutRelation . '.' . $morphKey, '.'),
                            1 === \count($group[$innerKey])
                                ? $this->expressionFactory->same(\reset($group[$innerKey]))
                                : $this->expressionFactory->in($group[$innerKey])
                        ),
                    ]);
                }, $valueGroups);

                return $this->dispatch(self::negateExpression(
                    1 === \count($disjuncts) ? \reset($disjuncts) : $this->expressionFactory->orX($disjuncts),
                    $isNegated
                ));
        }

        return null;
    }

    private static function negateExpression(Expression $expression, bool $negate = true): Expression
    {
        if (!$negate) {
            return $expression;
        }

        if ($expression instanceof Logic\AndX) {
            return new Logic\OrX($expression->getConjuncts());
        }

        if ($expression instanceof Logic\OrX) {
            return new Logic\AndX($expression->getDisjuncts());
        }

        if ($expression instanceof Logic\Not) {
            return $expression->getNegatedExpression();
        }

        return new Logic\Not($expression);
    }
}
