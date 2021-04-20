<?php

declare(strict_types=1);

namespace spaceonfire\Criteria\Bridge\DoctrineCollections;

use Doctrine\Common\Collections\Expr\Comparison;
use Doctrine\Common\Collections\Expr\CompositeExpression;
use Doctrine\Common\Collections\Expr\ExpressionVisitor;
use Doctrine\Common\Collections\Expr\Value;
use InvalidArgumentException;
use spaceonfire\Criteria\Criteria;
use spaceonfire\Criteria\Expression\ExpressionFactory;
use Webmozart\Assert\Assert;
use Webmozart\Expression\Expression;

/**
 * Converts Expressions from Doctrine collections to webmozart expressions
 */
class DoctrineCollectionsExpressionConverter extends ExpressionVisitor
{
    /**
     * @var string
     */
    private $comparisonMethod;

    /**
     * DoctrineCollectionsExpressionConverter constructor.
     * @param string $comparisonMethod `property` or `key`
     */
    public function __construct(string $comparisonMethod = 'property')
    {
        Assert::oneOf($comparisonMethod, ['property', 'key']);
        $this->comparisonMethod = $comparisonMethod;
    }

    /**
     * @inheritDoc
     * @return Expression
     */
    public function walkCompositeExpression(CompositeExpression $expr): Expression
    {
        $expressionList = [];
        foreach ($expr->getExpressionList() as $child) {
            $expressionList[] = $this->dispatch($child);
        }

        switch ($expr->getType()) {
            case CompositeExpression::TYPE_AND:
                return $this->getExpressionFactory()->andX($expressionList);

            case CompositeExpression::TYPE_OR:
                return $this->getExpressionFactory()->orX($expressionList);

            default:
                throw new InvalidArgumentException(sprintf(
                    'Unknown composite expression type: "%s"',
                    $expr->getType()
                ));
        }
    }

    /**
     * @inheritDoc
     * @return Expression
     */
    public function walkComparison(Comparison $comparison): Expression
    {
        $field = $comparison->getField();
        $value = $this->walkValue($comparison->getValue());

        switch ($comparison->getOperator()) {
            case Comparison::EQ:
            case Comparison::IS:
                $innerExpression = $this->getExpressionFactory()->same($value);
                break;

            case Comparison::NEQ:
                $innerExpression = $this->getExpressionFactory()->notSame($value);
                break;

            case Comparison::IN:
                $innerExpression = $this->getExpressionFactory()->in($value);
                break;

            case Comparison::NIN:
                $innerExpression = $this->getExpressionFactory()->not($this->getExpressionFactory()->in($value));
                break;

            case Comparison::CONTAINS:
                $innerExpression = $this->getExpressionFactory()->contains($value);
                break;

            case Comparison::STARTS_WITH:
                $innerExpression = $this->getExpressionFactory()->startsWith($value);
                break;

            case Comparison::ENDS_WITH:
                $innerExpression = $this->getExpressionFactory()->endsWith($value);
                break;

            case Comparison::LT:
                $innerExpression = $this->getExpressionFactory()->lessThan($value);
                break;

            case Comparison::LTE:
                $innerExpression = $this->getExpressionFactory()->lessThanEqual($value);
                break;

            case Comparison::GT:
                $innerExpression = $this->getExpressionFactory()->greaterThan($value);
                break;

            case Comparison::GTE:
                $innerExpression = $this->getExpressionFactory()->greaterThanEqual($value);
                break;
        }

        if (!isset($innerExpression)) {
            throw new InvalidArgumentException(sprintf(
                'Unknown comparison operator: "%s"',
                $comparison->getOperator()
            ));
        }

        return $this->getExpressionFactory()->{$this->comparisonMethod}($field, $innerExpression);
    }

    /**
     * @inheritDoc
     */
    public function walkValue(Value $value)
    {
        return $value->getValue();
    }

    private function getExpressionFactory(): ExpressionFactory
    {
        return Criteria::expr();
    }
}
