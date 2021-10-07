<?php

declare(strict_types=1);

namespace spaceonfire\Collection;

use PHPUnit\Framework\TestCase;
use spaceonfire\Common\Field\DefaultField;
use spaceonfire\Criteria\Criteria;
use spaceonfire\Criteria\Expression\ExpressionFactory;
use spaceonfire\Type\BuiltinType;

class CollectionTest extends TestCase
{
    public function testNewFromList(): void
    {
        $range = \range(0, 8);

        $collection = Collection::new($range);

        self::assertSame($range, $collection->all());
    }

    public function testNewFromArray(): void
    {
        $range = \range(0, 8);
        $chars = \array_map(static fn ($i) => \chr($i), $range);

        $collection = Collection::new(\array_combine($chars, $range));

        self::assertSame($range, \iterator_to_array($collection));
    }

    public function testNewFromOtherCollection(): void
    {
        $range = \range(0, 8);

        $collection = Collection::new($range);

        self::assertSame($collection, Collection::new($collection));
    }

    public function testNewWithValueType(): void
    {
        $this->expectException(\LogicException::class);

        \iterator_to_array(Collection::new(\range(0, 8), BuiltinType::string()));
    }

    public function testClear(): void
    {
        $collection = Collection::new(\range(0, 9));
        $collection->clear();

        self::assertSame([], $collection->all());
    }

    public function testAdd(): void
    {
        $collection = Collection::new();
        $collection->add(1);
        $collection->add(2, 3, 4, 5);

        self::assertSame(\range(1, 5), $collection->all());
        self::assertCount(5, $collection);
    }

    public function testAddTyped(): void
    {
        $this->expectException(\LogicException::class);
        $collection = Collection::new([], BuiltinType::string());
        $collection->add(1);
    }

    public function testRemove(): void
    {
        $collection = Collection::new(\range(0, 9));
        $collection->remove(0);
        $collection->remove(99);
        $collection->remove(7, 8, 9);

        self::assertSame(\range(1, 6), $collection->all());
    }

    public function testRemoveTyped(): void
    {
        $this->expectException(\LogicException::class);
        $collection = Collection::new(\range(0, 9), BuiltinType::int());
        $collection->remove('0');
    }

    public function testReplace(): void
    {
        $collection = Collection::new(\range(0, 9));

        $collection->replace(0, 10);
        $collection->replace(11, 0);

        self::assertSame([10, ...\range(1, 9)], $collection->all());
    }

    public function testReplaceTyped(): void
    {
        $this->expectException(\LogicException::class);

        $collection = Collection::new(\range(0, 9), BuiltinType::int());

        $collection->replace('0', '10');
    }

    public function testApplyOperation(): void
    {
        $collection = Collection::new(\range(0, 9));

        $dumbOperation = new class implements OperationInterface {
            public int $applyCounter = 0;

            public function apply(\Traversable $iterator): \Iterator
            {
                $this->applyCounter++;
                return yield from $iterator;
            }
        };

        $modifiedCollection = $collection->applyOperation($dumbOperation);

        self::assertCount(10, $modifiedCollection);
        self::assertCount(10, $modifiedCollection);
        self::assertSame(\range(0, 9), $modifiedCollection->all());
        self::assertSame(\range(0, 9), $modifiedCollection->all());
        self::assertSame(1, $dumbOperation->applyCounter);
    }

    public function testFilter(): void
    {
        $iterator = (static fn () => yield from \range(0, 9))();
        $collection = Collection::new($iterator);
        $filteredCollection = $collection->filter(static fn (int $v) => $v >= 5);

        self::assertNotSame($collection, $filteredCollection);
        self::assertSame(\range(5, 9), $filteredCollection->all());
        self::assertSame(\range(0, 9), $collection->all());
    }

    public function testFilterWithoutCallback(): void
    {
        $collection = Collection::new([null, false, 0, [], ...\range(1, 5)]);
        $filteredCollection = $collection->filter();

        self::assertNotSame($collection, $filteredCollection);
        self::assertSame(\range(1, 5), $filteredCollection->all());
    }

    public function testMap(): void
    {
        $collection = Collection::new(\range(0, 9));
        $mapCollection = $collection->map(static fn (int $v) => $v * 2);

        self::assertNotSame($collection, $mapCollection);
        self::assertSame(\range(0, 18, 2), $mapCollection->all());
    }

    public function testMapTyped(): void
    {
        $collection = Collection::new(\range(0, 9), BuiltinType::int());
        $callback = static fn (int $v) => \chr($v);
        $mapCollection = $collection->map($callback);

        self::assertSame(\array_map($callback, \range(0, 9)), $mapCollection->all());
    }

    public function testMapMultiple(): void
    {
        $this->skipIfXdebugEnabled();

        $collection = Collection::new(\range(1, 10000));
        $mapCallback = static fn (int $v) => $v;
        $mapCollection = $collection;

        $i = 1000;
        while ($i > 0) {
            $mapCollection = $mapCollection->map($mapCallback);
            $i--;
        }

        self::assertNotSame($collection, $mapCollection);
        self::assertSame(\range(1, 10000), $mapCollection->all());
    }

    public function testIterateAfterSomeOperation(): void
    {
        $collection = Collection::new(\range(0, 10000))->map(static fn ($i) => $i);

        $powItems = $collection->map(static fn ($i) => $i ** $i)->all();

        self::assertCount(10001, $powItems);
        self::assertSame(\range(0, 10000), $collection->all());
    }

    public function testReverse(): void
    {
        $collection = Collection::new(\range(0, 9));
        $reverseCollection = $collection->reverse();

        self::assertNotSame($collection, $reverseCollection);
        self::assertSame(\range(9, 0, -1), $reverseCollection->all());
    }

    public function testMerge(): void
    {
        $collection = Collection::new(\range(0, 9));
        $mergeCollection = $collection->merge(
            \range(10, 20, 2),
            \range(-10, 0),
        );

        self::assertNotSame($collection, $mergeCollection);
        self::assertSame(
            [...\range(0, 9), ...\range(10, 20, 2), ...\range(-10, 0)],
            $mergeCollection->all()
        );
    }

    public function testUnique(): void
    {
        $obj = (object)[];
        $res = \fopen('php://memory', 'r+b');
        $collection = Collection::new([
            null,
            null,
            true,
            true,
            false,
            false,
            [],
            [],
            $obj,
            $obj,
            $res,
            $res,
            ...\range(0, 9),
            ...\range(0, 9),
            3.14,
            3.14,
        ]);
        $uniqueCollection = $collection->unique();

        self::assertNotSame($collection, $uniqueCollection);
        self::assertSame(
            [
                null,
                true,
                false,
                [],
                $obj,
                $res,
                ...\range(0, 9),
                3.14,
            ],
            $uniqueCollection->all()
        );

        \fclose($res);
    }

    public function testUniqueLargeCollection(): void
    {
        $this->skipIfXdebugEnabled();

        $iterator = (static function () {
            $range = \range(1, 10000);
            \shuffle($range);
            foreach ($range as $v) {
                $i = 100;

                while ($i > 0) {
                    yield $v;
                    $i--;
                }
            }
        })();

        $collection = Collection::new($iterator);
        $uniqueCollection = $collection->unique();

        self::assertNotSame($collection, $uniqueCollection);
        self::assertCount(10000, $uniqueCollection);
    }

    public function testSort(): void
    {
        $array = \range(0, 9);
        \shuffle($array);

        $collection = Collection::new($array);
        $sortCollection = $collection->sort(null, \SORT_ASC);

        \array_multisort($array, \SORT_ASC);

        self::assertNotSame($collection, $sortCollection);
        self::assertSame($array, $sortCollection->all());

        $sortCollection = $collection->sort(null, \SORT_DESC);

        \array_multisort($array, SORT_DESC);

        self::assertNotSame($collection, $sortCollection);
        self::assertSame($array, $sortCollection->all());
    }

    public function testSortInvalidDirection(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $array = \range(0, 9);
        \shuffle($array);

        $collection = Collection::new($array);
        $collection->sort(null, -1);
    }

    public function testSlice(): void
    {
        $collection = Collection::new(\range(0, 9));

        self::assertSame(\range(5, 9), $collection->slice(5)->all());
        self::assertSame(\range(2, 4), $collection->slice(2, 3)->all());
        self::assertSame([], $collection->slice(20, 3)->all());
        self::assertSame([9], $collection->slice(9, 3)->all());
    }

    public function testSliceInvalidOffset(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $collection = Collection::new(\range(0, 9));

        $collection->slice(-1);
    }

    public function testSliceInvalidLimit(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $collection = Collection::new(\range(0, 9));

        $collection->slice(0, 0);
    }

    public function testMatching(): void
    {
        $collection = Collection::new(\array_map(static fn ($value) => (object)['value' => $value], \range(0, 100)));

        $ef = ExpressionFactory::new();

        $criteria = Criteria::new(
            $ef->property('value', $ef->greaterThan(25)),
            ['value' => SORT_DESC],
            0,
            25
        );

        $result = $collection->matching($criteria);

        self::assertNotSame($collection, $result);
        self::assertCount(25, $result);
        self::assertSame(\range(100, 76, -1), $result->map(static fn ($v) => $v->value)->all());
    }

    public function testFind(): void
    {
        $iterator = (static fn () => yield from \range(0, 9))();
        $collection = Collection::new($iterator);

        $found = $collection->find(static fn (int $v) => $v === 5);

        self::assertSame(5, $found);

        $notFound = $collection->find(static fn (int $v) => $v === 10);

        self::assertNull($notFound);

        self::assertSame(\range(0, 9), $collection->all());
    }

    public function testContains(): void
    {
        $collection = Collection::new(\range(0, 9));

        self::assertTrue($collection->contains(5));
        self::assertFalse($collection->contains(10));
    }

    public function testFirst(): void
    {
        $iterator = (static fn () => yield from \range(0, 9))();
        $collection = Collection::new($iterator);

        self::assertSame(0, $collection->first());
        self::assertSame(\range(0, 9), $collection->all());
    }

    public function testFirstEmpty(): void
    {
        $collection = Collection::new();
        self::assertNull($collection->first());
    }

    public function testLast(): void
    {
        $iterator = (static fn () => yield from \range(0, 9))();
        $collection = Collection::new($iterator);

        self::assertSame(9, $collection->last());
        self::assertSame(\range(0, 9), $collection->all());
    }

    public function testLastEmpty(): void
    {
        $collection = Collection::new();

        self::assertNull($collection->last());
    }

    public function testReduce(): void
    {
        $iterator = (static fn () => yield from \range(0, 9))();
        $collection = Collection::new($iterator);

        self::assertSame(45, $collection->reduce(static fn ($accum, $val) => $accum + $val, 0));
        self::assertSame(\range(0, 9), $collection->all());
    }

    public function testImplode(): void
    {
        $collection = Collection::new(\range(0, 9));

        self::assertSame('0123456789', $collection->implode());
        self::assertSame('0, 1, 2, 3, 4, 5, 6, 7, 8, 9', $collection->implode(', '));
        self::assertSame(', 1, 2', Collection::new(['', '1', '2'])->implode(', '));
    }

    public function testSum(): void
    {
        self::assertSame(6, Collection::new([1, 2, 3])->sum());
        self::assertSame(6, Collection::new([
            ['field' => 1],
            ['field' => 2],
            ['field' => 3],
        ])->sum(new DefaultField('field')));
    }

    public function testSumNonNumeric(): void
    {
        $this->expectException(\LogicException::class);

        Collection::new(\array_map(static fn ($i) => \chr($i), \range(0, 9)))->sum();
    }

    public function testAverage(): void
    {
        self::assertEqualsWithDelta(2, Collection::new([1, 2, 3])->average(), PHP_FLOAT_EPSILON);
        self::assertNull(Collection::new()->average());

        $collection = Collection::new([
            ['field' => 1],
            ['field' => 2],
            ['field' => 3],
        ]);
        self::assertEqualsWithDelta(2, $collection->average(new DefaultField('field')), PHP_FLOAT_EPSILON);
    }

    public function testAverageNonNumeric(): void
    {
        $this->expectException(\LogicException::class);

        Collection::new(\array_map(static fn ($i) => \chr($i), \range(0, 9)))->average();
    }

    public function testMedian(): void
    {
        $even = [12, 21, 34, 34, 44, 54, 55, 77];
        \shuffle($even);

        $odd = [12, 21, 34, 34, 54, 55, 77];
        \shuffle($odd);

        self::assertEqualsWithDelta(39, Collection::new($even)->median(), PHP_FLOAT_EPSILON);
        self::assertEqualsWithDelta(34, Collection::new($odd)->median(), PHP_FLOAT_EPSILON);
        self::assertNull(Collection::new()->median());
    }

    public function testMedianNonNumeric(): void
    {
        $this->expectException(\LogicException::class);

        Collection::new(\array_map(static fn ($i) => \chr($i), \range(0, 9)))->median();
    }

    public function testMax(): void
    {
        self::assertSame(3, Collection::new([1, 2, 3])->max());

        $collection = Collection::new([
            ['field' => 1],
            ['field' => 2],
            ['field' => 3],
        ]);
        self::assertEquals(3, $collection->max(new DefaultField('field')));
    }

    public function testMaxNonNumeric(): void
    {
        $this->expectException(\LogicException::class);

        Collection::new(\array_map(static fn ($i) => \chr($i), \range(0, 9)))->max();
    }

    public function testMin(): void
    {
        self::assertSame(1, Collection::new([1, 2, 3])->min());
        self::assertSame(-3, Collection::new([1, 2, -3])->min());

        $collection = Collection::new([
            ['field' => 1],
            ['field' => 2],
            ['field' => -3],
        ]);
        self::assertEquals(-3, $collection->min(new DefaultField('field')));
    }

    public function testMinNonNumeric(): void
    {
        $this->expectException(\LogicException::class);

        Collection::new(\array_map(static fn ($i) => \chr($i), \range(0, 9)))->min();
    }

    public function testIndexBy(): void
    {
        $range = \range(0, 8);
        $keyExtractor = static fn ($i) => \chr($i);
        $chars = \array_map($keyExtractor, $range);

        $collection = Collection::new($range);
        $map = $collection->indexBy($keyExtractor);

        self::assertSame(\array_combine($chars, $range), \iterator_to_array($map));
    }

    public function testIndexByField(): void
    {
        $array = [
            [
                'key' => 'key1',
                'field' => 1,
            ],
            [
                'key' => 'key2',
                'field' => 2,
            ],
            [
                'key' => 'key3',
                'field' => -3,
            ],
        ];
        $keyExtractor = new DefaultField('key');
        $keys = \array_map(static fn ($i) => $keyExtractor->extract($i), $array);

        $collection = Collection::new($array);
        $map = $collection->indexBy($keyExtractor);

        self::assertSame(\array_combine($keys, $array), \iterator_to_array($map));
    }

    public function testIndexByStringable(): void
    {
        $stringableFactory = static fn (string $v) => new class($v) {
            private string $v;

            public function __construct(string $v)
            {
                $this->v = $v;
            }

            public function __toString()
            {
                return $this->v;
            }
        };

        $array = [
            [
                'key' => $stringableFactory('key1'),
                'field' => 1,
            ],
            [
                'key' => $stringableFactory('key2'),
                'field' => 2,
            ],
            [
                'key' => $stringableFactory('key3'),
                'field' => -3,
            ],
        ];

        $keyExtractor = new DefaultField('key');
        $keys = \array_map(static fn ($i) => $keyExtractor->extract($i), $array);

        $collection = Collection::new($array);
        $map = $collection->indexBy($keyExtractor);

        self::assertSame(\array_combine($keys, $array), \iterator_to_array($map));
    }

    public function testIndexByInvalidKeyExtractor(): void
    {
        $this->expectException(\LogicException::class);

        $collection = Collection::new();

        $collection->indexBy(null);
    }

    public function testGroupBy(): void
    {
        $collection = Collection::new([
            ['id' => 'user-1', 'value' => 'John Doe', 'group' => 'group-1'],
            ['id' => 'user-2', 'value' => 'Jane Doe', 'group' => 'group-1'],
            ['id' => 'user-3', 'value' => 'Johnson Doe', 'group' => 'group-2'],
            ['id' => 'user-4', 'value' => 'Janifer Doe', 'group' => 'group-2'],
            ['id' => 'user-5', 'value' => 'Janifer Doe', 'group' => 'group-1'],
        ]);

        $map = $collection->groupBy(static fn ($i) => $i['group']);

        self::assertTrue($map->has('group-1'));
        self::assertTrue($map->has('group-2'));
        self::assertCount(2, $map);
        self::assertCount(3, $map->get('group-1'));
        self::assertCount(2, $map->get('group-2'));
    }

    public function testGroupByField(): void
    {
        $collection = Collection::new([
            ['id' => 'user-1', 'value' => 'John Doe', 'group' => 'group-1'],
            ['id' => 'user-2', 'value' => 'Jane Doe', 'group' => 'group-1'],
            ['id' => 'user-3', 'value' => 'Johnson Doe', 'group' => 'group-2'],
            ['id' => 'user-4', 'value' => 'Janifer Doe', 'group' => 'group-2'],
            ['id' => 'user-5', 'value' => 'Janifer Doe', 'group' => 'group-1'],
        ]);

        $map = $collection->groupBy(new DefaultField('group'));

        self::assertTrue($map->has('group-1'));
        self::assertTrue($map->has('group-2'));
        self::assertCount(2, $map);
        self::assertCount(3, $map->get('group-1'));
        self::assertCount(2, $map->get('group-2'));
    }

    public function testGetIterator(): void
    {
        $collection = Collection::new(\range(0, 9));

        self::assertSame(\range(0, 9), iterator_to_array($collection->getIterator()));
    }

    public function testGetIteratorMultiple(): void
    {
        $this->skipIfXdebugEnabled();

        $range = \range(1, 10000);

        $collection = Collection::new($range);

        $i = 5000;

        while ($i > 0) {
            self::assertSame($range, iterator_to_array($collection->getIterator()));
            $i--;
        }
    }

    public function testCount(): void
    {
        $collection = Collection::new(\range(0, 9));

        self::assertCount(10, $collection);
    }

    public function testCountAfterOperation(): void
    {
        $collection = Collection::new(\range(0, 9))->filter(static fn (int $v) => $v >= 5);

        self::assertCount(5, $collection);
    }

    public function testJsonSerialize(): void
    {
        $collection = Collection::new(\range(0, 9));

        self::assertJsonStringEqualsJsonString(
            \json_encode(\range(0, 9), JSON_THROW_ON_ERROR),
            \json_encode($collection, JSON_THROW_ON_ERROR)
        );
    }

    public function testJsonSerializeAfterOperation(): void
    {
        $collection = Collection::new(\range(0, 9))->filter(static fn (int $v) => $v >= 5);

        self::assertJsonStringEqualsJsonString(
            \json_encode(\range(5, 9), JSON_THROW_ON_ERROR),
            \json_encode($collection, JSON_THROW_ON_ERROR)
        );
    }

    private function skipIfXdebugEnabled(): void
    {
        if (!\extension_loaded('xdebug')) {
            return;
        }

        $xdebugModeEnv = \trim((string)(\getenv('XDEBUG_MODE') ?: ''));
        $xdebugModeIni = \trim((string)(\ini_get('xdebug.mode') ?: ''));
        $xdebugMode = $xdebugModeEnv ?: $xdebugModeIni;

        if ('' === $xdebugMode) {
            return;
        }

        if (\strpos($xdebugMode, 'off') !== false) {
            return;
        }

        $this->markTestSkipped('Test skipped because xdebug enabled.');
    }
}
