<?php

declare(strict_types=1);

namespace Warp\Criteria\Expression;

use Warp\Common\Factory\SingletonStorageTrait;
use Warp\Common\Factory\StaticConstructorInterface;
use Warp\Common\Field\FieldFactoryAggregate;
use Warp\Common\Field\FieldFactoryInterface;
use Warp\Common\Field\FieldInterface;
use Webmozart\Expression\Constraint\Contains;
use Webmozart\Expression\Constraint\EndsWith;
use Webmozart\Expression\Constraint\Equals;
use Webmozart\Expression\Constraint\GreaterThan;
use Webmozart\Expression\Constraint\GreaterThanEqual;
use Webmozart\Expression\Constraint\In;
use Webmozart\Expression\Constraint\IsEmpty;
use Webmozart\Expression\Constraint\IsInstanceOf;
use Webmozart\Expression\Constraint\KeyExists;
use Webmozart\Expression\Constraint\KeyNotExists;
use Webmozart\Expression\Constraint\LessThan;
use Webmozart\Expression\Constraint\LessThanEqual;
use Webmozart\Expression\Constraint\Matches;
use Webmozart\Expression\Constraint\NotEquals;
use Webmozart\Expression\Constraint\NotSame;
use Webmozart\Expression\Constraint\Same;
use Webmozart\Expression\Constraint\StartsWith;
use Webmozart\Expression\Expr;
use Webmozart\Expression\Expression;
use Webmozart\Expression\Logic\AlwaysFalse;
use Webmozart\Expression\Logic\AlwaysTrue;
use Webmozart\Expression\Logic\AndX;
use Webmozart\Expression\Logic\Not;
use Webmozart\Expression\Logic\OrX;
use Webmozart\Expression\Selector\All;
use Webmozart\Expression\Selector\AtLeast;
use Webmozart\Expression\Selector\AtMost;
use Webmozart\Expression\Selector\Count;
use Webmozart\Expression\Selector\Exactly;
use Webmozart\Expression\Selector\Method;

/**
 * @method Not not(Expression $expr)
 * @method AndX andX(Expression[] $conjuncts)
 * @method OrX orX(Expression[] $disjuncts)
 * @method AlwaysTrue true()
 * @method AlwaysFalse false()
 * @method Method method(string $methodName, mixed[] $args, Expression $expr)
 * @method AtLeast atLeast(int $count, Expression $expr)
 * @method AtMost atMost(int $count, Expression $expr)
 * @method Exactly exactly(int $count, Expression $expr)
 * @method All all(Expression $expr)
 * @method Count count(Expression $expr)
 * @method Same null()
 * @method NotSame notNull()
 * @method IsEmpty isEmpty()
 * @method Not notEmpty()
 * @method IsInstanceOf isInstanceOf(string $className)
 * @method Equals equals(mixed $value)
 * @method NotEquals notEquals(mixed $value)
 * @method Same same(mixed $value)
 * @method NotSame notSame(mixed $value)
 * @method GreaterThan greaterThan(mixed $value)
 * @method GreaterThanEqual greaterThanEqual(mixed $value)
 * @method LessThan lessThan(mixed $value)
 * @method LessThanEqual lessThanEqual(mixed $value)
 * @method In in(mixed[] $values)
 * @method Matches matches(string $regExp)
 * @method KeyExists keyExists(string $keyName)
 * @method KeyNotExists keyNotExists(string $keyName)
 */
final class ExpressionFactory implements StaticConstructorInterface
{
    use SingletonStorageTrait;

    /**
     * @see Expr::all()
     * @see Expr::andX()
     * @see Expr::atLeast()
     * @see Expr::atMost()
     * @see Expr::contains()
     * @see Expr::count()
     * @see Expr::endsWith()
     * @see Expr::equals()
     * @see Expr::exactly()
     * @see Expr::false()
     * @see Expr::greaterThan()
     * @see Expr::greaterThanEqual()
     * @see Expr::in()
     * @see Expr::isEmpty()
     * @see Expr::isInstanceOf()
     * @see Expr::keyExists()
     * @see Expr::keyNotExists()
     * @see Expr::lessThan()
     * @see Expr::lessThanEqual()
     * @see Expr::matches()
     * @see Expr::method()
     * @see Expr::not()
     * @see Expr::notEmpty()
     * @see Expr::notEquals()
     * @see Expr::notNull()
     * @see Expr::notSame()
     * @see Expr::null()
     * @see Expr::orX()
     * @see Expr::same()
     * @see Expr::startsWith()
     * @see Expr::true()
     */
    private const PROXY_METHODS = [
        'all' => true,
        'andX' => true,
        'atLeast' => true,
        'atMost' => true,
        'contains' => true,
        'count' => true,
        'endsWith' => true,
        'equals' => true,
        'exactly' => true,
        'false' => true,
        'greaterThan' => true,
        'greaterThanEqual' => true,
        'in' => true,
        'isEmpty' => true,
        'isInstanceOf' => true,
        'keyExists' => true,
        'keyNotExists' => true,
        'lessThan' => true,
        'lessThanEqual' => true,
        'matches' => true,
        'method' => true,
        'not' => true,
        'notEmpty' => true,
        'notEquals' => true,
        'notNull' => true,
        'notSame' => true,
        'null' => true,
        'orX' => true,
        'same' => true,
        'startsWith' => true,
        'true' => true,
    ];

    private ?FieldFactoryInterface $fieldFactory = null;

    private function __construct()
    {
        self::singletonAttach($this);
    }

    public function __destruct()
    {
        self::singletonDetach($this);
    }

    /**
     * @param string $name
     * @param mixed[] $arguments
     * @return Expression
     */
    public function __call(string $name, array $arguments = [])
    {
        if (!isset(self::PROXY_METHODS[$name])) {
            throw new \BadMethodCallException(\sprintf('Call to an undefined method %s::%s().', self::class, $name));
        }

        return \call_user_func_array([Expr::class, $name], $arguments);
    }

    public static function new(): self
    {
        return self::singletonFetch(self::singletonKey()) ?? new self();
    }

    public function setFieldFactory(FieldFactoryInterface $fieldFactory): void
    {
        $this->fieldFactory = $fieldFactory;
    }

    /**
     * @param string|FieldInterface $field
     * @param Expression $expression
     * @return Selector
     */
    public function selector($field, Expression $expression): Selector
    {
        return new Selector($this->makeField($field), $expression);
    }

    /**
     * Check that the value of an array key matches an expression.
     * @param array-key $key The array key.
     * @param Expression $expr The evaluated expression.
     * @return Selector
     */
    public function key($key, Expression $expr): Selector
    {
        return $this->selector('[' . $key . ']', $expr);
    }

    /**
     * Check that the value of a object property/array key/mixed matches an expression.
     * @param string|FieldInterface $propertyName The name of the property.
     * @param Expression $expr The evaluated expression.
     * @return Selector
     */
    public function property($propertyName, Expression $expr): Selector
    {
        return $this->selector($propertyName, $expr);
    }

    public function startsWith(string $substring, bool $caseSensitive = true): Substring
    {
        return Substring::startsWith($substring, $caseSensitive);
    }

    public function endsWith(string $substring, bool $caseSensitive = true): Substring
    {
        return Substring::endsWith($substring, $caseSensitive);
    }

    public function contains(string $substring, bool $caseSensitive = true): Substring
    {
        return Substring::contains($substring, $caseSensitive);
    }

    /**
     * @param self $value
     * @return string
     */
    protected static function singletonKey($value = null): string
    {
        return self::class;
    }

    /**
     * @param FieldInterface|\Stringable|string|mixed $field
     * @return FieldInterface
     */
    private function makeField($field): FieldInterface
    {
        if ($field instanceof FieldInterface) {
            return $field;
        }

        if (\is_object($field) && \method_exists($field, '__toString')) {
            $field = (string)$field;
        }

        if (\is_string($field)) {
            return ($this->fieldFactory ??= FieldFactoryAggregate::default())->make($field);
        }

        throw new \InvalidArgumentException(\sprintf(
            'Argument #1 ($field) expected to be a instance of FieldInterface, string or stringable object. Got: %s.',
            \get_debug_type($field),
        ));
    }
}
