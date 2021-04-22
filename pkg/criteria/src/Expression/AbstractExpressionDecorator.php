<?php

declare(strict_types=1);

namespace spaceonfire\Criteria\Expression;

use spaceonfire\Common\Field\FieldInterface;
use Webmozart\Expression\Expression;
use Webmozart\Expression\Logic;

/**
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
 * @method Logic\AndX andProperty(string|FieldInterface $propertyName, Expression $expr)
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
 * @method Logic\OrX orProperty(string|FieldInterface $propertyName, Expression $expr)
 * @method Logic\AlwaysTrue orTrue()
 * @method self orFalse()
 */
abstract class AbstractExpressionDecorator implements Expression
{
    private Expression $expr;

    public function __construct(Expression $expr)
    {
        $this->expr = $expr;
    }

    /**
     * Magic logical chaining.
     * @param string $name
     * @param mixed[] $arguments
     * @return Expression
     * @see ExpressionFactory
     */
    public function __call(string $name, array $arguments = [])
    {
        $isAnd = \str_starts_with($name, 'and');
        $isOr = \str_starts_with($name, 'or');

        if (!$isAnd && !$isOr) {
            throw new \BadMethodCallException(\sprintf('Call to an undefined method %s::%s()', static::class, $name));
        }

        $factoryMethod = \lcfirst(\substr($name, $isAnd ? 3 : 2));

        if (\str_starts_with($factoryMethod, 'and') || \str_starts_with($factoryMethod, 'or')) {
            throw new \BadMethodCallException(\sprintf('Call to an undefined method %s::%s()', static::class, $name));
        }

        $expr = \call_user_func_array([ExpressionFactory::new(), $factoryMethod], $arguments);

        return $isAnd ? $this->andX($expr) : $this->orX($expr);
    }

    public function getInnerExpression(bool $recursive = true): Expression
    {
        if ($recursive && $this->expr instanceof self) {
            return $this->expr->getInnerExpression();
        }

        return $this->expr;
    }

    public function evaluate($value): bool
    {
        return $this->expr->evaluate($value);
    }

    public function equivalentTo(Expression $other): bool
    {
        if (!$other instanceof static) {
            return false;
        }

        return $this->expr->equivalentTo($other->expr);
    }

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
}
