<?php

declare(strict_types=1);

namespace spaceonfire\Collection;

use BadMethodCallException;
use PHPUnit\Framework\TestCase;

class CollectionTest extends TestCase
{
    public function testFromArray()
    {
        $collection = new Collection([1, 2, 3]);
        $this->assertEquals([1, 2, 3], $collection->all());
    }

    public function testFromOtherCollection()
    {
        $first = new Collection([1, 2, 3]);
        $second = new Collection($first);
        $this->assertEquals($first->all(), $second->all());
    }

    public function testSum()
    {
        $collection = new Collection([1, 2, 3]);
        $this->assertEquals(6, $collection->sum());
    }

    public function testSumException()
    {
        $this->expectException(BadMethodCallException::class);
        $collection = new Collection([1, 'test', 3]);
        $collection->sum();
    }

    public function testSumWithField()
    {
        $collection = new Collection([
            ['field' => 1],
            ['field' => 2],
            ['field' => 3],
        ]);
        $this->assertEquals(6, $collection->sum('field'));
    }

    public function testMax()
    {
        $collection = new Collection([1, 2, 3]);
        $this->assertEquals(3, $collection->max());
    }

    public function testMaxException()
    {
        $this->expectException(BadMethodCallException::class);
        $collection = new Collection([1, 'test', 3]);
        $collection->max();
    }

    public function testMaxWithField()
    {
        $collection = new Collection([
            ['field' => 1],
            ['field' => 2],
            ['field' => 3],
        ]);
        $this->assertEquals(3, $collection->max('field'));
    }

    public function testMin()
    {
        $collection = new Collection([1, 2, -3]);
        $this->assertEquals(-3, $collection->min());
    }

    public function testMinException()
    {
        $this->expectException(BadMethodCallException::class);
        $collection = new Collection([1, 'test', 3]);
        $collection->min();
    }

    public function testMinWithField()
    {
        $collection = new Collection([
            ['field' => 1],
            ['field' => 2],
            ['field' => -3],
        ]);
        $this->assertEquals(-3, $collection->min('field'));
    }

    public function testIsEmpty()
    {
        $collection = new Collection([1, 2, -3]);
        $this->assertFalse($collection->isEmpty());
        $collection = new Collection();
        $this->assertTrue($collection->isEmpty());
    }

    public function testMerge()
    {
        $first = new Collection([
            'one' => 1,
            'two' => 2,
        ]);
        $second = new Collection([10, 20, 30]);
        $result = $first->merge($second, [['field' => 1]]);
        $this->assertEquals([
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
        $collection = new Collection([
            ['id' => 'user-1', 'value' => 'John Doe'],
            ['id' => 'user-2', 'value' => 'Jane Doe'],
        ]);
        $this->assertEquals([
            'user-1' => 'John Doe',
            'user-2' => 'Jane Doe',
        ], $collection->remap('id', 'value')->all());
    }

    public function testIndexBy()
    {
        $collection = new Collection([
            ['id' => 'user-1', 'value' => 'John Doe'],
            ['id' => 'user-2', 'value' => 'Jane Doe'],
        ]);
        $items = $collection->indexBy('id')->all();
        $this->assertArrayHasKey('user-1', $items);
        $this->assertArrayHasKey('user-2', $items);
    }

    public function testGroupBy()
    {
        $collection = new Collection([
            ['id' => 'user-1', 'value' => 'John Doe', 'group' => 'group-1'],
            ['id' => 'user-2', 'value' => 'Jane Doe', 'group' => 'group-1'],
            ['id' => 'user-3', 'value' => 'Johnson Doe', 'group' => 'group-2'],
            ['id' => 'user-4', 'value' => 'Janifer Doe', 'group' => 'group-2'],
        ]);

        $groupedCollection = $collection->groupBy('group');

        $this->assertArrayHasKey('group-1', $groupedCollection->all());
        $this->assertArrayHasKey('group-2', $groupedCollection->all());
        $this->assertInstanceOf(Collection::class, $groupedCollection['group-1']);
        $this->assertInstanceOf(Collection::class, $groupedCollection['group-2']);

        $groupedCollection = $collection->groupBy('group', false);

        $this->assertArrayHasKey('group-1', $groupedCollection->all());
        $this->assertArrayHasKey('group-2', $groupedCollection->all());
        $this->assertInstanceOf(Collection::class, $groupedCollection['group-1']);
        $this->assertInstanceOf(Collection::class, $groupedCollection['group-2']);
    }

    public function testContains()
    {
        $collection = new Collection([1, '2', 3]);
        $this->assertTrue($collection->contains(2));
        $this->assertFalse($collection->contains(2, true));
        $this->assertFalse($collection->contains(10));
        $this->assertTrue($collection->contains(static function ($item) {
            return $item === '2';
        }));
        $this->assertFalse($collection->contains(static function ($item) {
            return $item === 2;
        }));
    }

    public function testRemove()
    {
        $collection = new Collection([1, '2', 3]);
        $this->assertEquals([1, 2 => 3], $collection->remove(2)->all());
        $collection = new Collection([1, '2', 3]);
        $this->assertEquals([1, '2', 3], $collection->remove(2, true)->all());
        $collection = new Collection([1, '2', '3']);
        $this->assertEquals([1], $collection->remove(static function ($item) {
            return is_string($item);
        })->all());
    }

    public function testFilter()
    {
        $collection = new Collection([1, 2, 3, 4, 5, 6, 0]);
        $this->assertEquals([1, 2, 3, 4, 5, 6], $collection->filter()->all());
    }

    public function testFilterWithCallback()
    {
        $collection = new Collection([1, 2, 3, 4, 5, 6]);
        $this->assertEquals([1 => 2, 3 => 4, 5 => 6], $collection->filter(static function ($item) {
            return $item % 2 === 0;
        })->all());
    }

    public function testFind()
    {
        $collection = new Collection([1, 2, 3, 4, 5, 6]);
        $this->assertEquals(2, $collection->find(static function ($item) {
            return $item % 2 === 0;
        }));
        $this->assertNull($collection->find(static function ($item) {
            return $item === 100;
        }));
    }

    public function testReplace()
    {
        $collection = new Collection([1, '2', 3]);
        $this->assertEquals([1, 5, 3], $collection->replace(2, 5)->all());
        $this->assertNotEquals([1, 5, 3], $collection->replace(2, 5, true)->all());
    }

    public function testSlice()
    {
        $collection = new Collection([1, '2', 3]);
        $this->assertEquals([1 => '2', 2 => 3], $collection->slice(1)->all());
    }

    public function testToJson()
    {
        $collection = new Collection([1, '2', 3]);
        $this->assertJson($collection->toJson());
    }
}
