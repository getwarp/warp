<?php

declare(strict_types=1);

namespace spaceonfire\Collection;

use ArrayIterator;
use BadMethodCallException;
use PHPUnit\Framework\TestCase;
use spaceonfire\Criteria\Criteria;
use stdClass;

abstract class AbstractCollectionTest extends TestCase
{
    abstract protected function factory($items = []): CollectionInterface;

    public function testFromArray()
    {
        $collection = $this->factory([1, 2, 3]);
        self::assertEquals([1, 2, 3], $collection->all());
    }

    public function testFromOtherCollection()
    {
        $first = $this->factory([1, 2, 3]);
        $second = $this->factory($first);
        self::assertEquals($first->all(), $second->all());
    }

    public function testFromJsonSerializable()
    {
        $json = new class implements \JsonSerializable {
            /**
             * @inheritDoc
             */
            public function jsonSerialize()
            {
                return [
                    'one' => 1,
                    'two' => 2,
                ];
            }
        };
        $collection = $this->factory($json);
        self::assertEquals(['one' => 1, 'two' => 2], $collection->all());
    }

    public function testFromObject()
    {
        $object = (object)['one' => 1, 'two' => 2];
        $collection = $this->factory($object);
        self::assertEquals(['one' => 1, 'two' => 2], $collection->all());
    }

    public function testFromIterator()
    {
        $collection = $this->factory(new ArrayIterator([1, 2, 3]));
        self::assertEquals([1, 2, 3], $collection->all());
    }

    public function testClear(): void
    {
        $collection = $this->factory([1, 2, 3]);
        $collection->clear();
        self::assertEquals([], $collection->all());
    }

    public function testEach(): void
    {
        $collection = $this->factory([1, 2, 3]);
        $callsCounter = 0;

        $collection->each(static function () use (&$callsCounter) {
            $callsCounter++;
        });
        self::assertEquals(3, $callsCounter);

        $callsCounter = 0;

        $collection->each(static function ($val) use (&$callsCounter) {
            $callsCounter++;

            if ($val === 2) {
                return false;
            }

            return null;
        });
        self::assertEquals(2, $callsCounter);
    }

    public function testReduce()
    {
        $collection = $this->factory([1, 2, 3]);

        self::assertEquals(6, $collection->reduce(static function ($accum, $val) {
            return $accum * $val;
        }, 1));
    }

    public function testSum()
    {
        $collection = $this->factory([1, 2, 3]);
        self::assertEquals(6, $collection->sum());
    }

    public function testSumException()
    {
        $this->expectException(BadMethodCallException::class);
        $collection = $this->factory([1, 'test', 3]);
        $collection->sum();
    }

    public function testSumWithField()
    {
        $collection = $this->factory([
            ['field' => 1],
            ['field' => 2],
            ['field' => 3],
        ]);
        self::assertEquals(6, $collection->sum('field'));
    }

    public function testMax()
    {
        $collection = $this->factory([1, 2, 3]);
        self::assertEquals(3, $collection->max());
    }

    public function testMaxException()
    {
        $this->expectException(BadMethodCallException::class);
        $collection = $this->factory([1, 'test', 3]);
        $collection->max();
    }

    public function testMaxWithField()
    {
        $collection = $this->factory([
            ['field' => 1],
            ['field' => 2],
            ['field' => 3],
        ]);
        self::assertEquals(3, $collection->max('field'));
    }

    public function testMin()
    {
        $collection = $this->factory([1, 2, -3]);
        self::assertEquals(-3, $collection->min());
    }

    public function testMinException()
    {
        $this->expectException(BadMethodCallException::class);
        $collection = $this->factory([1, 'test', 3]);
        $collection->min();
    }

    public function testMinWithField()
    {
        $collection = $this->factory([
            ['field' => 1],
            ['field' => 2],
            ['field' => -3],
        ]);
        self::assertEquals(-3, $collection->min('field'));
    }

    public function testIsEmpty()
    {
        $collection = $this->factory([1, 2, -3]);
        self::assertFalse($collection->isEmpty());
        $collection = $this->factory();
        self::assertTrue($collection->isEmpty());
    }

    public function testMap(): void
    {
        $collection = $this->factory([1, 3, 2]);

        self::assertEquals([2, 6, 4], $collection->map(static function ($val) {
            return $val * 2;
        })->all());
    }

    public function testSort(): void
    {
        $collection = $this->factory([1, 3, 2]);
        self::assertEquals([1, 2, 3], $collection->sort()->values()->all());
        self::assertEquals([3, 2, 1], $collection->sort(SORT_DESC)->values()->all());
    }

    public function testSortByKey(): void
    {
        $collection = $this->factory(['a' => 1, 'c' => 3, 'b' => 2]);
        self::assertEquals(['a' => 1, 'b' => 2, 'c' => 3], $collection->sortByKey()->all());
        self::assertEquals(['c' => 3, 'b' => 2, 'a' => 1], $collection->sortByKey(SORT_DESC)->all());
    }

    public function testSortNatural(): void
    {
        $collection = $this->factory(['IMG0.png', 'img12.png', 'img10.png', 'img2.png', 'img1.png', 'IMG3.png']);
        self::assertEquals(
            ['IMG0.png', 'img1.png', 'img2.png', 'IMG3.png', 'img10.png', 'img12.png'],
            $collection->sortNatural()->values()->all()
        );
        self::assertEquals(
            ['IMG0.png', 'IMG3.png', 'img1.png', 'img2.png', 'img10.png', 'img12.png'],
            $collection->sortNatural(true)->values()->all()
        );
    }

    public function testSortBy(): void
    {
        $collection = $this->factory([
            ['id' => 'user-4', 'value' => 'Janifer Doe'],
            ['id' => 'user-1', 'value' => 'John Doe'],
            ['id' => 'user-3', 'value' => 'Johnson Doe'],
            ['id' => 'user-2', 'value' => 'Jane Doe'],
        ]);

        self::assertEquals(
            [
                ['id' => 'user-1', 'value' => 'John Doe'],
                ['id' => 'user-2', 'value' => 'Jane Doe'],
                ['id' => 'user-3', 'value' => 'Johnson Doe'],
                ['id' => 'user-4', 'value' => 'Janifer Doe'],
            ],
            $collection->sortBy('id')->values()->all()
        );
    }

    public function testReverse()
    {
        $collection = $this->factory([1, 2, 3]);
        self::assertEquals([3, 2, 1], array_values($collection->reverse()->all()));
    }

    public function testValues()
    {
        $collection = $this->factory(['a' => 1, 'b' => 2, 'c' => 3]);
        self::assertEquals([1, 2, 3], $collection->values()->all());
    }

    public function testKeys()
    {
        $collection = $this->factory(['a' => 1, 'b' => 2, 'c' => 3]);
        self::assertEquals(['a', 'b', 'c'], $collection->keys()->all());
    }

    public function testFlip()
    {
        $collection = $this->factory(['a' => 1, 'b' => 2, 'c' => 3]);
        self::assertEquals([1 => 'a', 2 => 'b', 3 => 'c'], $collection->flip()->all());
    }

    public function testMerge()
    {
        $first = $this->factory([
            'one' => 1,
            'two' => 2,
        ]);
        $second = $this->factory([10, 20, 30]);
        $result = $first->merge($second, [['field' => 1]]);
        self::assertEquals([
            'one' => 1,
            'two' => 2,
            10,
            20,
            30,
            ['field' => 1]
        ], $result->all());
    }

    public function testRemap()
    {
        $collection = $this->factory([
            ['id' => 'user-1', 'value' => 'John Doe'],
            ['id' => 'user-2', 'value' => 'Jane Doe'],
        ]);
        self::assertEquals([
            'user-1' => 'John Doe',
            'user-2' => 'Jane Doe',
        ], $collection->remap('id', 'value')->all());
    }

    public function testIndexBy()
    {
        $collection = $this->factory([
            ['id' => 'user-1', 'value' => 'John Doe'],
            ['id' => 'user-2', 'value' => 'Jane Doe'],
        ]);
        $items = $collection->indexBy('id')->all();
        self::assertArrayHasKey('user-1', $items);
        self::assertArrayHasKey('user-2', $items);
    }

    public function testGroupBy()
    {
        $collection = $this->factory([
            ['id' => 'user-1', 'value' => 'John Doe', 'group' => 'group-1'],
            ['id' => 'user-2', 'value' => 'Jane Doe', 'group' => 'group-1'],
            ['id' => 'user-3', 'value' => 'Johnson Doe', 'group' => 'group-2'],
            ['id' => 'user-4', 'value' => 'Janifer Doe', 'group' => 'group-2'],
        ]);

        $groupedCollection = $collection->groupBy('group');

        self::assertArrayHasKey('group-1', $groupedCollection->all());
        self::assertArrayHasKey('group-2', $groupedCollection->all());
        self::assertInstanceOf(Collection::class, $groupedCollection['group-1']);
        self::assertInstanceOf(Collection::class, $groupedCollection['group-2']);

        $groupedCollection = $collection->groupBy('group', false);

        self::assertArrayHasKey('group-1', $groupedCollection->all());
        self::assertArrayHasKey('group-2', $groupedCollection->all());
        self::assertInstanceOf(Collection::class, $groupedCollection['group-1']);
        self::assertInstanceOf(Collection::class, $groupedCollection['group-2']);
    }

    public function testContains()
    {
        $collection = $this->factory([1, '2', 3]);
        self::assertTrue($collection->contains(2));
        self::assertFalse($collection->contains(2, true));
        self::assertFalse($collection->contains(10));
        self::assertTrue($collection->contains(static function ($item) {
            return $item === '2';
        }));
        self::assertFalse($collection->contains(static function ($item) {
            return $item === 2;
        }));
    }

    public function testRemove()
    {
        $collection = $this->factory([1, '2', 3]);
        self::assertEquals([1, 2 => 3], $collection->remove(2)->all());
        $collection = $this->factory([1, '2', 3]);
        self::assertEquals([1, '2', 3], $collection->remove(2, true)->all());
        $collection = $this->factory([1, '2', '3']);
        self::assertEquals([1], $collection->remove(static function ($item) {
            return is_string($item);
        })->all());
    }

    public function testFilter()
    {
        $collection = $this->factory([1, 2, 3, 4, 5, 6, 0]);
        self::assertEquals([1, 2, 3, 4, 5, 6], $collection->filter()->all());
    }

    public function testFilterWithCallback()
    {
        $collection = $this->factory([1, 2, 3, 4, 5, 6]);
        self::assertEquals([1 => 2, 3 => 4, 5 => 6], $collection->filter(static function ($item) {
            return $item % 2 === 0;
        })->all());
    }

    public function testFind()
    {
        $collection = $this->factory([1, 2, 3, 4, 5, 6]);
        self::assertEquals(2, $collection->find(static function ($item) {
            return $item % 2 === 0;
        }));
        self::assertNull($collection->find(static function ($item) {
            return $item === 100;
        }));
    }

    public function testReplace()
    {
        $collection = $this->factory([1, '2', 3]);
        self::assertEquals([1, 5, 3], $collection->replace(2, 5)->all());
        self::assertNotEquals([1, 5, 3], $collection->replace(2, 5, true)->all());
    }

    public function testSlice()
    {
        $collection = $this->factory([1, '2', 3]);
        self::assertEquals([1 => '2', 2 => 3], $collection->slice(1)->all());
    }

    public function testMatching()
    {
        $items = array_map(static function ($val) {
            $object = new stdClass();
            $object->value = $val;
            return $object;
        }, range(0, 100));
        $collection = $this->factory($items);

        $criteria = new Criteria(
            Criteria::expr()->property('value', Criteria::expr()->greaterThan(25)),
            ['value' => SORT_DESC],
            0,
            25
        );

        $result = $collection->matching($criteria);

        self::assertCount(25, $result);
    }

    public function testUnique()
    {
        $collection = $this->factory([1, 2, 2, 3, 3, 3]);
        self::assertEquals([0 => 1, 1 => 2, 3 => 3], $collection->unique()->all());
    }

    public function testImplodeStrings()
    {
        $collection = $this->factory(['hello', 'world']);
        self::assertEquals('hello world', $collection->implode(' '));
    }

    public function testImplodeObjects()
    {
        $stringableFactory = function (string $value) {
            return new class($value) {
                private $value;

                public function __construct($value)
                {
                    $this->value = $value;
                }

                public function __toString(): string
                {
                    return $this->value;
                }
            };
        };
        $collection = $this->factory([
            $stringableFactory('hello'),
            $stringableFactory('world'),
        ]);
        self::assertEquals('hello world', $collection->implode(' '));
    }

    public function testImplodeWihField()
    {
        $objectFactory = function (string $value) {
            return new class($value) {
                public $value;

                public function __construct($value)
                {
                    $this->value = $value;
                }
            };
        };

        $collection = $this->factory([
            $objectFactory('hello'),
            $objectFactory('world'),
        ]);

        self::assertEquals('hello world', $collection->implode(' ', 'value'));
    }

    public function testImplodeFail()
    {
        $objectFactory = function (string $value) {
            return new class($value) {
                public $value;

                public function __construct($value)
                {
                    $this->value = $value;
                }
            };
        };
        $this->expectException(BadMethodCallException::class);
        $collection = $this->factory([
            $objectFactory('hello'),
            $objectFactory('world'),
        ]);
        $collection->implode(' ');
    }

    public function testJoinAlias()
    {
        $collection = $this->factory(['hello', 'world']);
        self::assertEquals('hello world', $collection->join(' '));
    }

    public function testFirst()
    {
        $collection = $this->factory([1, '2', 3]);
        self::assertEquals(1, $collection->first());
    }

    public function testFirstEmpty()
    {
        $collection = $this->factory();
        self::assertEquals(null, $collection->first());
    }

    public function testFirstKey()
    {
        $collection = $this->factory(['one' => 1, 'two' => '2', 'three' => 3]);
        self::assertEquals('one', $collection->firstKey());
    }

    public function testFirstKeyEmpty()
    {
        $collection = $this->factory();
        self::assertEquals(null, $collection->firstKey());
    }

    public function testLast()
    {
        $collection = $this->factory([1, '2', 3]);
        self::assertEquals(3, $collection->last());
    }

    public function testLastEmpty()
    {
        $collection = $this->factory();
        self::assertEquals(null, $collection->last());
    }

    public function testLastKey()
    {
        $collection = $this->factory(['one' => 1, 'two' => '2', 'three' => 3]);
        self::assertEquals('three', $collection->lastKey());
    }

    public function testLastKeyEmpty()
    {
        $collection = $this->factory();
        self::assertEquals(null, $collection->lastKey());
    }

    public function testOffsetExists(): void
    {
        $collection = $this->factory(['one' => 1, 'two' => '2', 'three' => 3]);
        self::assertTrue($collection->offsetExists('one'));
        self::assertFalse($collection->offsetExists('four'));
    }

    public function testOffsetSet(): void
    {
        $collection = $this->factory();
        $collection[] = 1;
        $collection['two'] = 2;
        self::assertEquals([1, 'two' => 2], $collection->all());
    }

    public function testOffsetUnset(): void
    {
        $collection = $this->factory(['one' => 1, 'two' => '2', 'three' => 3]);
        self::assertTrue($collection->offsetExists('one'));
        unset($collection['one']);
        self::assertFalse($collection->offsetExists('one'));
    }

    public function testAverage()
    {
        $collection = $this->factory([1, '2', 3]);
        self::assertEqualsWithDelta(2, $collection->average(), 0.01);
    }

    public function testAverageEmpty()
    {
        $collection = $this->factory();
        self::assertNull($collection->average());
    }

    public function testAverageWithField()
    {
        $collection = $this->factory([
            ['field' => 1],
            ['field' => 2],
            ['field' => 3],
        ]);
        self::assertEqualsWithDelta(2, $collection->average('field'), 0.01);
    }

    public function testAvgAlias()
    {
        $collection = $this->factory([1, '2', 3]);
        self::assertEqualsWithDelta(2, $collection->avg(), 0.01);
    }

    public function testMedian()
    {
        $dataSet = [12, 21, 34, 34, 44, 54, 55, 77];
        shuffle($dataSet);
        $collection = $this->factory($dataSet);
        self::assertEqualsWithDelta(39, $collection->median(), 0.01);
    }

    public function testMedianOdd()
    {
        $dataSet = [12, 21, 34, 34, 54, 55, 77];
        shuffle($dataSet);
        $collection = $this->factory($dataSet);
        self::assertEqualsWithDelta(34, $collection->median(), 0.01);
    }

    public function testMedianEmpty()
    {
        $collection = $this->factory();
        self::assertEquals(null, $collection->median());
    }

    public function testMedianException()
    {
        $this->expectException(BadMethodCallException::class);
        $collection = $this->factory(['a', 'b', 'c']);
        $collection->median();
    }

    public function testGetIterator()
    {
        $collection = $this->factory(['a', 'b', 'c']);
        self::assertIsIterable($collection->getIterator());
    }

    public function testJsonSerialize()
    {
        $collection = $this->factory(['a', 'b', 'c']);
        self::assertJson(json_encode($collection));
    }

    public function testToJson()
    {
        $collection = $this->factory([1, '2', 3]);
        self::assertJson($collection->toJson());
    }

    public function testStringify()
    {
        $collection = $this->factory([1, '2', 3]);
        self::assertJson((string)$collection);
    }

    public function testCallUndefinedMethod()
    {
        $this->expectException(BadMethodCallException::class);
        $collection = $this->factory([1, '2', 3]);

        /** @noinspection PhpUndefinedMethodInspection */
        $collection->undefindeMethod();
    }
}
