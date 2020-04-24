<?php

declare(strict_types=1);

namespace spaceonfire\Criteria\Adapter\DoctrineCollections;

use Doctrine\Common\Collections\Expr\Comparison;
use Doctrine\Common\Collections\Expr\CompositeExpression;
use Doctrine\Common\Collections\Expr\ExpressionVisitor;
use Doctrine\Common\Collections\Expr\Value;
use InvalidArgumentException;
use spaceonfire\Criteria\Criteria;
use spaceonfire\Criteria\Expression\ExpressionBuilder;
use Webmozart\Assert\Assert;
use Webmozart\Expression\Expression;

/**
 * Converts Expressions from Doctrine collections to webmozart expressions
 * @package spaceonfire\Criteria\Adapter\DoctrineCollections
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

    private function getExpressionBuilder(): ExpressionBuilder
    {
        return Criteria::expr();
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
                return $this->getExpressionBuilder()->andX($expressionList);

            case CompositeExpression::TYPE_OR:
                return $this->getExpressionBuilder()->orX($expressionList);

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
                $innerExpression = $this->getExpressionBuilder()->same($value);
                break;

            case Comparison::NEQ:
                $innerExpression = $this->getExpressionBuilder()->notSame($value);
                break;

            case Comparison::IN:
                $innerExpression = $this->getExpressionBuilder()->in($value);
                break;

            case Comparison::NIN:
                $innerExpression = $this->getExpressionBuilder()->not($this->getExpressionBuilder()->in($value));
                break;

            case Comparison::CONTAINS:
                $innerExpression = $this->getExpressionBuilder()->contains($value);
                break;

            case Comparison::STARTS_WITH:
                $innerExpression = $this->getExpressionBuilder()->startsWith($value);
                break;

            case Comparison::ENDS_WITH:
                $innerExpression = $this->getExpressionBuilder()->endsWith($value);
                break;

            case Comparison::LT:
                $innerExpression = $this->getExpressionBuilder()->lessThan($value);
                break;

            case Comparison::LTE:
                $innerExpression = $this->getExpressionBuilder()->lessThanEqual($value);
                break;

            case Comparison::GT:
                $innerExpression = $this->getExpressionBuilder()->greaterThan($value);
                break;

            case Comparison::GTE:
                $innerExpression = $this->getExpressionBuilder()->greaterThanEqual($value);
                break;
        }

        if (!isset($innerExpression)) {
            throw new InvalidArgumentException(sprintf(
                'Unknown comparison operator: "%s"',
                $comparison->getOperator()
            ));
        }

        return $this->getExpressionBuilder()->{$this->comparisonMethod}($field, $innerExpression);
    }

    /**
     * @inheritDoc
     */
    public function walkValue(Value $value)
    {
        return $value->getValue();
    }
}
