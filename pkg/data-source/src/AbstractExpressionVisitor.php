<?php

declare(strict_types=1);

namespace spaceonfire\DataSource;

use spaceonfire\Common\Field\FieldInterface;
use spaceonfire\Criteria\Expression\AbstractExpressionDecorator;
use spaceonfire\Criteria\Expression\ExpressionFactory;
use spaceonfire\Criteria\Expression\Selector;
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

        $expression = $selector->getExpression();
        $isNegated = false;

        if ($expression instanceof Logic\Not) {
            $expression = $expression->getNegatedExpression();
            $isNegated = true;
        }

        return $this->visitComparison($selector->getField(), $expression, $isNegated);
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

    final protected function negateExpression(Expression $expressionToNegate): Expression
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
