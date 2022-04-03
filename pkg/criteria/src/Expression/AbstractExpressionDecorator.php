<?php

declare(strict_types=1);

namespace Warp\Criteria\Expression;

use Symfony\Component\PropertyAccess\PropertyPath;
use Warp\Criteria\Criteria;
use Webmozart\Expression\Expression;
use Webmozart\Expression\Logic;

/**
 * Class AbstractExpressionDecorator.
 *
 * @method Logic\AndX andNot(Expression $expr)
 * @method Logic\AndX andMethod(string $methodName, mixed[] $args, Expression $expr)
 * @method Logic\AndX andAtLeast(int $count, Expression $expr)
 * @method Logic\AndX andAtMost(int $count, Expression $expr)
 * @method Logic\AndX andExactly(int $count, Expression $expr)
 * @method Logic\AndX andAll(Expression $expr)
 * @method Logic\AndX andCount(Expression $expr)
 * @method Logic\AndX andNull()
 * @method Logic\AndX andNotNull()
 * @method Logic\AndX andIsEmpty()
 * @method Logic\AndX andNotEmpty()
 * @method Logic\AndX andIsInstanceOf(string $className)
 * @method Logic\AndX andEquals(mixed $value)
 * @method Logic\AndX andNotEquals(mixed $value)
 * @method Logic\AndX andSame(mixed $value)
 * @method Logic\AndX andNotSame(mixed $value)
 * @method Logic\AndX andGreaterThan(mixed $value)
 * @method Logic\AndX andGreaterThanEqual(mixed $value)
 * @method Logic\AndX andLessThan(mixed $value)
 * @method Logic\AndX andLessThanEqual(mixed $value)
 * @method Logic\AndX andIn(mixed[] $values)
 * @method Logic\AndX andMatches(string $regExp)
 * @method Logic\AndX andStartsWith(string $prefix)
 * @method Logic\AndX andEndsWith(string $suffix)
 * @method Logic\AndX andContains(string $string)
 * @method Logic\AndX andKeyExists(string $keyName)
 * @method Logic\AndX andKeyNotExists(string $keyName)
 * @method Logic\AndX andKey(string|int $key, Expression $expr)
 * @method Logic\AndX andProperty(string|PropertyPath $propertyName, Expression $expr)
 * @method self andTrue()
 * @method Logic\AlwaysFalse andFalse()
 * @method Logic\OrX orNot(Expression $expr)
 * @method Logic\OrX orMethod(string $methodName, mixed[] $args, Expression $expr)
 * @method Logic\OrX orAtLeast(int $count, Expression $expr)
 * @method Logic\OrX orAtMost(int $count, Expression $expr)
 * @method Logic\OrX orExactly(int $count, Expression $expr)
 * @method Logic\OrX orAll(Expression $expr)
 * @method Logic\OrX orCount(Expression $expr)
 * @method Logic\OrX orNull()
 * @method Logic\OrX orNotNull()
 * @method Logic\OrX orIsEmpty()
 * @method Logic\OrX orNotEmpty()
 * @method Logic\OrX orIsInstanceOf(string $className)
 * @method Logic\OrX orEquals(mixed $value)
 * @method Logic\OrX orNotEquals(mixed $value)
 * @method Logic\OrX orSame(mixed $value)
 * @method Logic\OrX orNotSame(mixed $value)
 * @method Logic\OrX orGreaterThan(mixed $value)
 * @method Logic\OrX orGreaterThanEqual(mixed $value)
 * @method Logic\OrX orLessThan(mixed $value)
 * @method Logic\OrX orLessThanEqual(mixed $value)
 * @method Logic\OrX orIn(mixed[] $values)
 * @method Logic\OrX orMatches(string $regExp)
 * @method Logic\OrX orStartsWith(string $prefix)
 * @method Logic\OrX orEndsWith(string $suffix)
 * @method Logic\OrX orContains(string $string)
 * @method Logic\OrX orKeyExists(string $keyName)
 * @method Logic\OrX orKeyNotExists(string $keyName)
 * @method Logic\OrX orKey(string|int $key, Expression $expr)
 * @method Logic\OrX orProperty(string|PropertyPath $propertyName, Expression $expr)
 * @method Logic\AlwaysTrue orTrue()
 * @method self orFalse()
 */
class AbstractExpressionDecorator implements Expression
{
    /**
     * @var Expression
     */
    private $expr;

    /**
     * Expression Adapter constructor
     * @param Expression $expr
     */
    public function __construct(Expression $expr)
    {
        $this->expr = $expr;
    }

    /**
     * @param string $name
     * @param array $arguments
     * @return Expression
     * @see ExpressionFactory
     */
    public function __call(string $name, array $arguments = [])
    {
        if (null !== $expr = $this->magicLogicalExpression($name, $arguments)) {
            return $expr;
        }

        throw new \BadMethodCallException(sprintf('Call to an undefined method %s::%s()', static::class, $name));
    }

    /**
     * Getter for `expr` property
     * @param bool $recursive
     * @return Expression
     */
    public function getInnerExpression(bool $recursive = true): Expression
    {
        if ($recursive && $this->expr instanceof self) {
            return $this->expr->getInnerExpression();
        }

        return $this->expr;
    }

    /**
     * @inheritDoc
     */
    public function evaluate($value): bool
    {
        return $this->expr->evaluate($value);
    }

    /**
     * @inheritDoc
     */
    public function equivalentTo(Expression $other): bool
    {
        if (static::class !== get_class($other)) {
            return false;
        }

        /** @var self $other */
        return $this->expr->equivalentTo($other->expr);
    }

    /**
     * @inheritDoc
     */
    public function toString(): string
    {
        return $this->expr->toString();
    }

    public function andX(Expression $expr): Expression
    {
        $innerExpression = $this->getInnerExpression();

        if ($innerExpression instanceof Logic\AndX) {
            return $innerExpression->andX($expr);
        }

        if ($expr instanceof Logic\AlwaysTrue) {
            return $this;
        }

        if ($expr instanceof Logic\AlwaysFalse) {
            return $expr;
        }

        if ($this->equivalentTo($expr)) {
            return $this;
        }

        return new Logic\AndX([$this, $expr]);
    }

    public function orX(Expression $expr): Expression
    {
        $innerExpression = $this->getInnerExpression();

        if ($innerExpression instanceof Logic\OrX) {
            return $innerExpression->orX($expr);
        }

        if ($expr instanceof Logic\AlwaysFalse) {
            return $this;
        }

        if ($expr instanceof Logic\AlwaysTrue) {
            return $expr;
        }

        if ($this->equivalentTo($expr)) {
            return $this;
        }

        return new Logic\OrX([$this, $expr]);
    }

    private function magicLogicalExpression(string $name, array $arguments): ?Expression
    {
        $isAnd = 0 === strpos($name, 'and');
        $isOr = 0 === strpos($name, 'or');

        if (!$isAnd && !$isOr) {
            return null;
        }

        $factoryMethod = lcfirst(substr($name, $isAnd ? 3 : 2));

        if (0 === stripos($factoryMethod, 'and') || 0 === stripos($factoryMethod, 'or')) {
            return null;
        }

        $factory = [Criteria::expr(), $factoryMethod];

        if (!is_callable($factory)) {
            return null;
        }

        $expr = call_user_func_array($factory, $arguments);

        return $isAnd ? $this->andX($expr) : $this->orX($expr);
    }
}
