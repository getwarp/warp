<?php

declare(strict_types=1);

namespace spaceonfire\DataSource\Adapters\CycleOrm\Query;

use Cycle\ORM\Select\QueryBuilder;
use Cycle\ORM\Select\RootLoader;
use InvalidArgumentException;
use spaceonfire\DataSource\Adapters\CycleOrm\AbstractCycleOrmTest;
use spaceonfire\DataSource\Fixtures\Infrastructure\Mapper\StubMapper;
use Spiral\Database\Query\SelectQuery;
use Webmozart\Expression\Expr;
use Webmozart\Expression\Expression;
use Webmozart\Expression\Logic\AlwaysTrue;
use Webmozart\Expression\Selector\Key;

class CycleQueryExpressionVisitorTest extends AbstractCycleOrmTest
{
    /**
     * @var CycleQueryExpressionVisitor
     */
    private $visitor;
    /**
     * @var QueryBuilder
     */
    private $queryBuilder;

    protected function setUp(): void
    {
        parent::setUp();
        $this->visitor = new CycleQueryExpressionVisitor(new StubMapper());
        /** @var SelectQuery $query */
        $query = (new SelectQuery(['table'], []))->withDriver(
            self::getDriver(),
            'expression-visitor-test'
        );
        $this->queryBuilder = new QueryBuilder($query, new RootLoader(self::getOrm(), 'post'));
    }

    private function getSqlInOneLine(?callable $callable = null): string
    {
        if ($callable !== null) {
            $callable($this->queryBuilder);
        }

        $sql = (string)$this->queryBuilder->getQuery();
        $sql = implode(' ', array_map('trim', explode("\n", $sql)));
        return str_replace(['expression-visitor-test', '"post".'], '', $sql);
    }

    /**
     * @dataProvider successDataProvider
     * @param Expression $expression
     * @param string $expectedSql
     */
    public function testDispatch(Expression $expression, string $expectedSql): void
    {
        $callable = $this->visitor->dispatch($expression);
        $sql = $this->getSqlInOneLine($callable);
        self::assertEquals($expectedSql, $sql);
    }

    /**
     * @dataProvider errorDataProvider
     * @param Expression $expression
     */
    public function testDispatchUnknown(Expression $expression): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->visitor->dispatch($expression);
    }

    public function successDataProvider(): array
    {
        return [
            [
                Expr::key('key', Expr::equals('value')),
                'SELECT * FROM "table" WHERE "key" = \'value\'',
            ],
            [
                new class('key', Expr::equals('value')) extends Key {
                },
                'SELECT * FROM "table" WHERE "key" = \'value\'',
            ],
            [
                Expr::property('key', Expr::not(Expr::same(null))),
                'SELECT * FROM "table" WHERE "key" IS NOT NULL',
            ],
            [
                Expr::property('key', Expr::not(Expr::equals('test'))),
                'SELECT * FROM "table" WHERE "key" <> \'test\'',
            ],
            [
                Expr::property('key', Expr::in([1, 2, 3])),
                'SELECT * FROM "table" WHERE "key" IN (1 ,2 ,3)',
            ],
            [
                Expr::property('key', Expr::not(Expr::in([1, 2, 3]))),
                'SELECT * FROM "table" WHERE "key" NOT IN (1 ,2 ,3)',
            ],
            [
                Expr::property('key', Expr::contains('hello')),
                'SELECT * FROM "table" WHERE "key" LIKE \'%hello%\'',
            ],
            [
                Expr::property('key', Expr::startsWith('hello')),
                'SELECT * FROM "table" WHERE "key" LIKE \'hello%\'',
            ],
            [
                Expr::property('key', Expr::endsWith('hello')),
                'SELECT * FROM "table" WHERE "key" LIKE \'%hello\'',
            ],
            [
                Expr::property('key', Expr::not(Expr::contains('hello'))),
                'SELECT * FROM "table" WHERE "key" NOT LIKE \'%hello%\'',
            ],
            [
                Expr::property('key', Expr::not(Expr::startsWith('hello'))),
                'SELECT * FROM "table" WHERE "key" NOT LIKE \'hello%\'',
            ],
            [
                Expr::property('key', Expr::not(Expr::endsWith('hello'))),
                'SELECT * FROM "table" WHERE "key" NOT LIKE \'%hello\'',
            ],
            [
                Expr::property('key', Expr::greaterThan(1)),
                'SELECT * FROM "table" WHERE "key" > 1',
            ],
            [
                Expr::property('key', Expr::lessThan(1)),
                'SELECT * FROM "table" WHERE "key" < 1',
            ],
            [
                Expr::property('key', Expr::greaterThanEqual(1)),
                'SELECT * FROM "table" WHERE "key" >= 1',
            ],
            [
                Expr::property('key', Expr::lessThanEqual(1)),
                'SELECT * FROM "table" WHERE "key" <= 1',
            ],
            [
                Expr::property('key', Expr::not(Expr::greaterThan(1))),
                'SELECT * FROM "table" WHERE "key" <= 1',
            ],
            [
                Expr::property('key', Expr::not(Expr::lessThan(1))),
                'SELECT * FROM "table" WHERE "key" >= 1',
            ],
            [
                Expr::property('key', Expr::not(Expr::greaterThanEqual(1))),
                'SELECT * FROM "table" WHERE "key" < 1',
            ],
            [
                Expr::property('key', Expr::not(Expr::lessThanEqual(1))),
                'SELECT * FROM "table" WHERE "key" > 1',
            ],
            [
                Expr::andX([
                    Expr::key('key', Expr::greaterThan(10)),
                    Expr::key('key', Expr::lessThan(100)),
                ]),
                'SELECT * FROM "table" WHERE ("key" > 10  )AND ("key" < 100  )',
            ],
            [
                Expr::orX([
                    Expr::key('key', Expr::greaterThan(10)),
                    Expr::key('key', Expr::lessThan(100)),
                ]),
                'SELECT * FROM "table" WHERE ("key" > 10  )OR ("key" < 100  )',
            ],
        ];
    }

    public function errorDataProvider(): array
    {
        return [
            [Expr::true()],
            [new class() extends AlwaysTrue {
            }],
            [Expr::property('key', Expr::not(Expr::true()))],
            [Expr::property('key', Expr::true())],
        ];
    }
}
