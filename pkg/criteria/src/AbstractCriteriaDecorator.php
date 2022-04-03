<?php

declare(strict_types=1);

namespace Warp\Criteria;

use Warp\Criteria\Expression\ExpressionFactory;
use Webmozart\Expression\Expression;

abstract class AbstractCriteriaDecorator implements CriteriaInterface
{
    /**
     * @var CriteriaInterface
     */
    protected $criteria;

    /**
     * CriteriaAdapter constructor.
     * @param CriteriaInterface $criteria
     */
    public function __construct(CriteriaInterface $criteria)
    {
        $this->criteria = $criteria;
    }

    /**
     * Returns adapted criteria
     * @param bool $recursive
     * @return CriteriaInterface
     */
    public function getInnerCriteria(bool $recursive = true): CriteriaInterface
    {
        if ($recursive && $this->criteria instanceof self) {
            return $this->criteria->getInnerCriteria();
        }

        return $this->criteria;
    }

    /**
     * @inheritDoc
     */
    public function getWhere(): ?Expression
    {
        return $this->proxyCall(__FUNCTION__, func_get_args());
    }

    /**
     * @inheritDoc
     */
    public function where(?Expression $expression): CriteriaInterface
    {
        $this->proxyCall(__FUNCTION__, func_get_args());
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function andWhere(Expression $expression): CriteriaInterface
    {
        $this->proxyCall(__FUNCTION__, func_get_args());
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function orWhere(Expression $expression): CriteriaInterface
    {
        $this->proxyCall(__FUNCTION__, func_get_args());
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getOrderBy(): array
    {
        return $this->proxyCall(__FUNCTION__, func_get_args());
    }

    /**
     * @inheritDoc
     */
    public function orderBy(array $orderBy): CriteriaInterface
    {
        $this->proxyCall(__FUNCTION__, func_get_args());
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getOffset(): int
    {
        return $this->proxyCall(__FUNCTION__, func_get_args());
    }

    /**
     * @inheritDoc
     */
    public function offset(?int $offset): CriteriaInterface
    {
        $this->proxyCall(__FUNCTION__, func_get_args());
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getLimit(): ?int
    {
        return $this->proxyCall(__FUNCTION__, func_get_args());
    }

    /**
     * @inheritDoc
     */
    public function limit(?int $limit): CriteriaInterface
    {
        $this->proxyCall(__FUNCTION__, func_get_args());
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getInclude(): array
    {
        return $this->proxyCall(__FUNCTION__, func_get_args());
    }

    /**
     * @inheritDoc
     */
    public function include(array $include): CriteriaInterface
    {
        $this->proxyCall(__FUNCTION__, func_get_args());
        return $this;
    }

    /**
     * @inheritDoc
     * The original criteria will not be changed, a new one will be returned instead.
     */
    public function merge(CriteriaInterface $criteria): CriteriaInterface
    {
        $clone = clone $this;
        $clone->criteria = $clone->proxyCall(__FUNCTION__, func_get_args());
        return $clone;
    }

    /**
     * @inheritDoc
     */
    public static function expr(): ExpressionFactory
    {
        return Criteria::expr();
    }

    /**
     * @param string $methodName
     * @param mixed[] $arguments
     * @return mixed
     */
    final protected function proxyCall(string $methodName, array $arguments = [])
    {
        $callable = [$this->criteria, $methodName];

        if (!is_callable($callable)) {
            throw new \BadMethodCallException(
                sprintf('Call to an undefined method %s::%s()', get_class($this->criteria), $methodName)
            );
        }

        return call_user_func_array($callable, $arguments);
    }
}
