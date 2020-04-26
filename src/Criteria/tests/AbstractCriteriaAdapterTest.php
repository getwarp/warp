<?php

declare(strict_types=1);

namespace spaceonfire\Criteria;

class AbstractCriteriaAdapterTest extends AbstractCriteriaTest
{
    private function factory(?CriteriaInterface $criteria = null): AbstractCriteriaAdapter
    {
        return new class($criteria ?? new Criteria()) extends AbstractCriteriaAdapter {
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
        self::assertInstanceOf(AbstractCriteriaAdapter::class, $outerCriteriaAdapter->getInnerCriteria(false));
    }
}
