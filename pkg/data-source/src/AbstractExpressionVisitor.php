<?php

declare(strict_types=1);

namespace Warp\DataSource;

use Warp\Common\Field\FieldInterface;
use Warp\Criteria\Expression\AbstractExpressionDecorator;
use Warp\Criteria\Expression\ExpressionFactory;
use Warp\Criteria\Expression\Selector;
use Warp\Criteria\Expression\Substring;
use Webmozart\Expression\Constraint;
use Webmozart\Expression\Expression;
use Webmozart\Expression\Logic;
use Webmozart\Expression\Selector\Key;
use Webmozart\Expression\Selector\Property;

/**
 * @template I
 * @template O
 */
abstract class AbstractExpressionVisitor
{
    protected ExpressionFactory $expressionFactory;

    public function __construct(?ExpressionFactory $expressionFactory = null)
    {
        $this->expressionFactory = $expressionFactory ?? ExpressionFactory::new();
    }

    /**
     * Generates Query Builder scope for given expression
     * @param Expression $expression
     * @return callable(I):O
     */
    public function dispatch(Expression $expression): callable
    {
        if ($expression instanceof AbstractExpressionDecorator) {
            return $this->visitExpressionAdapter($expression);
        }

        if ($expression instanceof Logic\AndX) {
            return $this->visitConjunction($expression);
        }

        if ($expression instanceof Logic\OrX) {
            return $this->visitDisjunction($expression);
        }

        if ($expression instanceof \Webmozart\Expression\Selector\Selector) {
            return $this->visitSelector($expression);
        }

        if ($expression instanceof Logic\AlwaysTrue) {
            return $this->visitAlwaysTrue($expression);
        }

        if ($expression instanceof Logic\AlwaysFalse) {
            return $this->visitAlwaysFalse($expression);
        }

        throw ExpressionNotSupportedException::new($expression);
    }

    /**
     * Visit inner expression for expression adapter
     * @param AbstractExpressionDecorator $expr
     * @return callable(I):O
     */
    public function visitExpressionAdapter(AbstractExpressionDecorator $expr): callable
    {
        return $this->dispatch($expr->getInnerExpression());
    }

    /**
     * Generates Query Builder scope for conjunction expression
     * @param Logic\AndX $expression
     * @return callable(I):O
     */
    abstract public function visitConjunction(Logic\AndX $expression): callable;

    /**
     * Generates Query Builder scope for disjunction expression
     * @param Logic\OrX $expression
     * @return callable(I):O
     */
    abstract public function visitDisjunction(Logic\OrX $expression): callable;

    public function visitAlwaysTrue(Logic\AlwaysTrue $expression): callable
    {
        throw ExpressionNotSupportedException::new($expression);
    }

    public function visitAlwaysFalse(Logic\AlwaysFalse $expression): callable
    {
        throw ExpressionNotSupportedException::new($expression);
    }

    /**
     * Generates Query Builder scope for field selector expression
     * @param Selector|Property|Key|\Webmozart\Expression\Selector\Selector $selector
     * @return callable(I):O
     */
    public function visitSelector(\Webmozart\Expression\Selector\Selector $selector): callable
    {
        if ($selector instanceof Property) {
            $selector = $this->expressionFactory->property($selector->getPropertyName(), $selector->getExpression());
        }

        if ($selector instanceof Key) {
            $selector = $this->expressionFactory->key($selector->getKey(), $selector->getExpression());
        }

        if (!$selector instanceof Selector) {
            throw ExpressionNotSupportedException::new($selector);
        }

        [$expression, $isNegated] = $this->prepareNegatedExpression($selector->getExpression());

        return $this->visitComparison($selector->getField(), $this->simplifyExpression($expression), $isNegated);
    }

    /**
     * Generates Query Builder scope for comparison expression on given field
     * @param FieldInterface $field
     * @param Expression $expression
     * @param bool $isNegated
     * @return callable(I):O
     */
    abstract public function visitComparison(
        FieldInterface $field,
        Expression $expression,
        bool $isNegated = false
    ): callable;

    /**
     * @param Expression $expression
     * @return array{Expression,bool}
     */
    private function prepareNegatedExpression(Expression $expression): array
    {
        if (!$expression instanceof Logic\Not) {
            return [$expression, false];
        }

        $expression = $expression->getNegatedExpression();

        if ($expression instanceof Constraint\Equals) {
            return [$this->expressionFactory->notEquals($expression->getComparedValue()), false];
        }

        if ($expression instanceof Constraint\Same) {
            return [$this->expressionFactory->notSame($expression->getComparedValue()), false];
        }

        if ($expression instanceof Constraint\NotEquals) {
            return [$this->expressionFactory->equals($expression->getComparedValue()), false];
        }

        if ($expression instanceof Constraint\NotSame) {
            return [$this->expressionFactory->same($expression->getComparedValue()), false];
        }

        if ($expression instanceof Constraint\GreaterThan) {
            return [$this->expressionFactory->lessThanEqual($expression->getComparedValue()), false];
        }

        if ($expression instanceof Constraint\GreaterThanEqual) {
            return [$this->expressionFactory->lessThan($expression->getComparedValue()), false];
        }

        if ($expression instanceof Constraint\LessThan) {
            return [$this->expressionFactory->greaterThanEqual($expression->getComparedValue()), false];
        }

        if ($expression instanceof Constraint\LessThanEqual) {
            return [$this->expressionFactory->greaterThan($expression->getComparedValue()), false];
        }

        if (
            $expression instanceof Substring ||
            $expression instanceof Constraint\Contains ||
            $expression instanceof Constraint\EndsWith ||
            $expression instanceof Constraint\StartsWith ||
            $expression instanceof Constraint\In
        ) {
            return [$expression, true];
        }

        throw ExpressionNotSupportedException::cannotBeNegated($expression);
    }

    private function simplifyExpression(Expression $expression): Expression
    {
        if ($expression instanceof Constraint\In) {
            $acceptedValues = $expression->getAcceptedValues();

            if (1 === \count($acceptedValues)) {
                return $expression->isStrict()
                    ? $this->expressionFactory->same(\reset($acceptedValues))
                    : $this->expressionFactory->equals(\reset($acceptedValues));
            }
        }

        if ($expression instanceof Constraint\Contains) {
            return Substring::contains($expression->getComparedValue());
        }

        if ($expression instanceof Constraint\StartsWith) {
            return Substring::startsWith($expression->getAcceptedPrefix());
        }

        if ($expression instanceof Constraint\EndsWith) {
            return Substring::endsWith($expression->getAcceptedSuffix());
        }

        return $expression;
    }
}
