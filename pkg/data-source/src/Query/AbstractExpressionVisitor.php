<?php

declare(strict_types=1);

namespace Warp\DataSource\Query;

use InvalidArgumentException;
use Warp\Criteria\Expression\AbstractExpressionDecorator;
use Warp\Criteria\Expression\Selector;
use Warp\DataSource\MapperInterface;
use Webmozart\Expression\Constraint;
use Webmozart\Expression\Expression;
use Webmozart\Expression\Logic;
use Webmozart\Expression\Selector as SelectorNS;

abstract class AbstractExpressionVisitor
{
    /**
     * @var MapperInterface
     */
    protected $mapper;

    /**
     * AbstractExpressionVisitor constructor.
     * @param MapperInterface $mapper
     */
    public function __construct(MapperInterface $mapper)
    {
        $this->mapper = $mapper;
    }

    /**
     * Generates Query Builder scope for given expression
     * @param Expression $expression
     * @return callable
     */
    public function dispatch(Expression $expression): callable
    {
        $methodsMap = [
            AbstractExpressionDecorator::class => 'visitExpressionAdapter',
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
                if (null !== $class && $expression instanceof $class) {
                    break;
                }
            }
        }

        if ($method) {
            return $this->{$method}($expression);
        }

        throw $this->makeNotSupportedExpression($expression);
    }

    /**
     * Visit inner expression for expression adapter
     * @param AbstractExpressionDecorator $expr
     * @return callable
     */
    public function visitExpressionAdapter(AbstractExpressionDecorator $expr): callable
    {
        return $this->dispatch($expr->getInnerExpression());
    }

    /**
     * Generates Query Builder scope for conjunction expression
     * @param Logic\AndX $expression
     * @return callable
     */
    abstract public function visitConjunction(Logic\AndX $expression): callable;

    /**
     * Generates Query Builder scope for disjunction expression
     * @param Logic\OrX $expression
     * @return callable
     */
    abstract public function visitDisjunction(Logic\OrX $expression): callable;

    /**
     * Generates Query Builder scope for field selector expression
     * @param Selector|SelectorNS\Property|SelectorNS\Key|SelectorNS\Selector $selector
     * @return callable
     */
    public function visitSelector(SelectorNS\Selector $selector): callable
    {
        if ($selector instanceof SelectorNS\Property) {
            $selector = Selector::makeFromProperty($selector);
        }

        if ($selector instanceof SelectorNS\Key) {
            $selector = Selector::makeFromKey($selector);
        }

        if (!$selector instanceof Selector) {
            throw $this->makeNotSupportedExpression($selector);
        }

        $expression = $selector->getExpression();
        $isNegated = false;

        if ($expression instanceof Logic\Not) {
            $expression = $expression->getNegatedExpression();
            $isNegated = true;
        }

        return $this->visitComparison(
            $this->mapper->convertNameToStorage(implode('.', $selector->getPropertyPath()->getElements())),
            $expression,
            $isNegated
        );
    }

    /**
     * Generates Query Builder scope for comparison expression on given field
     * @param string $field
     * @param Expression $expression
     * @param bool $isNegated
     * @return callable
     */
    abstract public function visitComparison(string $field, Expression $expression, bool $isNegated = false): callable;

    /**
     * Format expression value for storage
     * @param string $field
     * @param mixed $value
     * @return mixed
     */
    public function visitValue(string $field, $value)
    {
        return $this->mapper->convertValueToStorage($field, $value);
    }

    final protected function makeNotSupportedExpression(
        Expression $expression,
        ?string $message = null
    ): InvalidArgumentException {
        return new InvalidArgumentException(
            sprintf($message ?? 'Not supported expression class: "%s"', get_class($expression))
        );
    }

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
