<?php

declare(strict_types=1);

namespace spaceonfire\Criteria\Expression;

use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\PropertyAccess\PropertyPath;
use Webmozart\Expression\Expression;
use Webmozart\Expression\Logic\AndX;
use Webmozart\Expression\Logic\OrX;
use Webmozart\Expression\Selector\Key;
use Webmozart\Expression\Selector\Property;
use Webmozart\Expression\Selector\Selector as AbstractSelector;

class Selector extends AbstractSelector
{
    /**
     * @var PropertyPath
     */
    private $propertyPath;

    /**
     * Selector expression constructor.
     * @param string|PropertyPath $propertyPath The property path.
     * @param Expression $expr The expression to evaluate for the value of property path.
     */
    public function __construct($propertyPath, Expression $expr)
    {
        parent::__construct($expr);

        $this->propertyPath = new PropertyPath((string)$propertyPath);
    }

    /**
     * Converts webmozart's Key expression to Selector expression.
     * @param Key $expression
     * @return static
     */
    public static function makeFromKey(Key $expression): self
    {
        return new self('[' . $expression->getKey() . ']', $expression->getExpression());
    }

    /**
     * Converts webmozart's Property expression to Selector expression.
     * @param Property $expression
     * @return static
     */
    public static function makeFromProperty(Property $expression): self
    {
        return new self($expression->getPropertyName(), $expression->getExpression());
    }

    /**
     * Returns the property path.
     * @return PropertyPath
     */
    public function getPropertyPath(): PropertyPath
    {
        return $this->propertyPath;
    }

    /**
     * @inheritDoc
     */
    public function evaluate($value): bool
    {
        return $this->expr->evaluate($this->getPropertyAccessor()->getValue($value, $this->propertyPath));
    }

    /**
     * @inheritdoc
     */
    public function equivalentTo(Expression $other): bool
    {
        if (!parent::equivalentTo($other)) {
            return false;
        }

        /** @var self $other */
        return (string)$this->propertyPath === (string)$other->propertyPath;
    }

    /**
     * @inheritDoc
     */
    public function toString(): string
    {
        $exprString = $this->expr->toString();

        $propertyPath = implode('.', $this->propertyPath->getElements());

        if ($this->expr instanceof AndX || $this->expr instanceof OrX) {
            return $propertyPath . '{' . $exprString . '}';
        }

        // Append "functions" with "."
        if (isset($exprString[0]) && ctype_alpha($exprString[0])) {
            return $propertyPath . '.' . $exprString;
        }

        return $propertyPath . $exprString;
    }

    private function getPropertyAccessor(): PropertyAccessorInterface
    {
        static $propertyAccessor;

        if (null === $propertyAccessor) {
            $propertyAccessor = PropertyAccess::createPropertyAccessorBuilder()
                ->disableExceptionOnInvalidIndex()
                ->disableExceptionOnInvalidPropertyPath()
                ->getPropertyAccessor();
        }

        return $propertyAccessor;
    }
}
