<?php

declare(strict_types=1);

namespace Warp\Criteria;

class AbstractCriteriaDecoratorTest extends AbstractCriteriaTest
{
    private function factory(?CriteriaInterface $criteria = null): AbstractCriteriaDecorator
    {
        return new class($criteria ?? new Criteria()) extends AbstractCriteriaDecorator {
            public function proxyCallUnknown(): void
            {
                $this->proxyCall('unknownMethodName');
            }
        };
    }

    protected function createCriteria(): CriteriaInterface
    {
        return $this->factory();
    }

    public function testGetInnerCriteria(): void
    {
        $innerCriteria = new Criteria();
        $middleCriteriaAdapter = $this->factory($innerCriteria);
        $outerCriteriaAdapter = $this->factory($middleCriteriaAdapter);

        self::assertEquals($innerCriteria, $outerCriteriaAdapter->getInnerCriteria());
        self::assertInstanceOf(AbstractCriteriaDecorator::class, $outerCriteriaAdapter->getInnerCriteria(false));
    }

    public function testProxyCallUnknownMethod(): void
    {
        $this->expectException(\BadMethodCallException::class);
        $criteria = $this->factory();
        $criteria->proxyCallUnknown();
    }
}
