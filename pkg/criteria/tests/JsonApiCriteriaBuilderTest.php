<?php

declare(strict_types=1);

namespace spaceonfire\Criteria;

use PHPUnit\Framework\TestCase;

class JsonApiCriteriaBuilderTest extends TestCase
{
    /**
     * @dataProvider buildProvider
     * @param array $methods
     * @param array $assertions
     * @param array $expectExceptions
     */
    public function testBuild(array $methods, array $assertions, array $expectExceptions): void
    {
        foreach ($expectExceptions as $exception) {
            $this->expectException($exception);
        }

        $builder = new JsonApiCriteriaBuilder();

        foreach ($methods as $method => $attributes) {
            $builder = call_user_func_array([$builder, $method], $attributes);
        }

        $criteria = $builder->build();

        foreach ($assertions as $assertion) {
            $assertMethod = array_shift($assertion);
            $criteriaMethod = array_pop($assertion);
            $assertion[] = $criteria->$criteriaMethod();
            call_user_func_array([$this, $assertMethod], $assertion);
        }
    }

    public function buildProvider(): array
    {
        return [
            [
                [
                    'withPage' => [1],
                    'withPageSize' => [75],
                    'withPageSizeRange' => [[50, 250]],
                    'withSort' => ['-testDesc,testAsc'],
                    'withInclude' => [['includeA', 'includeB.includeC']],
                ],
                [
                    ['assertEquals', 75, 'getLimit'],
                    ['assertEquals', 0, 'getOffset'],
                    ['assertEquals', ['testDesc' => SORT_DESC, 'testAsc' => SORT_ASC], 'getOrderBy'],
                    ['assertEquals', ['includeA', 'includeB.includeC'], 'getInclude'],
                ],
                [],
            ],
//            [
//                [
//                    'withPage' => [-1],
//                ],
//                [],
//                [
//                    InvalidArgumentException::class,
//                ],
//            ],
//            [
//                [
//                    'withPageSize' => [-75],
//                ],
//                [],
//                [
//                    InvalidArgumentException::class,
//                ],
//            ],
//            [
//                [
//                    'withPageSizeRange' => [[-50, -250]],
//                ],
//                [],
//                [
//                    InvalidArgumentException::class,
//                ],
//            ],
            [
                [
                    'withPage' => [5],
                    'withPageSize' => [25],
                    'withPageSizeRange' => [[250, 50]],
                    'withSort' => ['-testDesc,testAsc'],
                    'withInclude' => [['includeA', 'includeB.includeC']],
                ],
                [
                    ['assertEquals', 50, 'getLimit'],
                    ['assertEquals', 200, 'getOffset'],
                ],
                [],
            ],
            [
                [
                    'withPage' => [1],
                    'withPageSize' => [500],
                    'withPageSizeRange' => [[250, 50]],
                    'withSort' => ['-testDesc,testAsc'],
                    'withInclude' => [['includeA', 'includeB.includeC']],
                ],
                [
                    ['assertEquals', 250, 'getLimit'],
                ],
                [],
            ],
            [
                [
                    'withSort' => ['-testDesc,testAsc'],
                    'withAllowedOrderByFields' => [['testAsc']],
                ],
                [
                    ['assertEquals', ['testAsc' => SORT_ASC], 'getOrderBy'],
                ],
                [],
            ],
        ];
    }
}
