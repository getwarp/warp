<?php

declare(strict_types=1);

namespace spaceonfire\Collection;

use PHPUnit\Framework\TestCase;
use RuntimeException;

class TypedCollectionTest extends TestCase
{
    public function testScalar()
    {
        new TypedCollection([0, 1, 2], 'integer');
        $this->addToAssertionCount(1);
    }

    public function testScalarException()
    {
        $this->expectException(RuntimeException::class);
        new TypedCollection([0, 1, 2], 'string');
    }

    public function testObject()
    {
        new TypedCollection($this->getObjectsArray(), \stdClass::class);
        $this->addToAssertionCount(1);
    }

    public function testObjectException()
    {
        $this->expectException(RuntimeException::class);
        new TypedCollection($this->getObjectsArray(), CollectionInterface::class);
    }

    public function testObjectClassExistException()
    {
        $this->expectException(RuntimeException::class);
        new TypedCollection($this->getObjectsArray(), 'double');
    }

    public function testOffsetSet()
    {
        $collection = new TypedCollection([0, 1, 2], 'integer');
        $collection[] = 3;
        $this->addToAssertionCount(1);
    }

    public function testOffsetSetException()
    {
        $this->expectException(RuntimeException::class);
        $collection = new TypedCollection([0, 1, 2], 'integer');
        $collection[] = '3';
    }

    public function testDowngrade()
    {
        $collection = new TypedCollection($this->getObjectsArray(), \stdClass::class);
        $downgrade = $collection->downgrade();
        $this->assertNotEquals(TypedCollection::class, get_class($downgrade));
    }

    public function testKeys()
    {
        $collection = new TypedCollection([
            'one' => 1,
            'two' => 2,
        ], 'integer');

        $this->assertEquals(['one', 'two'], $collection->keys()->all());
    }

    public function testFlip()
    {
        $collection = new TypedCollection([
            'one' => 1,
            'two' => 2,
        ], 'integer');

        $flipped = $collection->flip();

        $this->assertNotEquals(TypedCollection::class, get_class($flipped));
        $this->assertEquals([1 => 'one', 2 => 'two'], $flipped->all());
    }

    public function testRemap()
    {
        $collection = new TypedCollection([
            ['id' => 'user-1', 'value' => 'John Doe'],
            ['id' => 'user-2', 'value' => 'Jane Doe'],
        ], 'array');

        $remapped = $collection->remap('id', 'value');

        $this->assertNotEquals(TypedCollection::class, get_class($remapped));
        $this->assertEquals([
            'user-1' => 'John Doe',
            'user-2' => 'Jane Doe',
        ], $remapped->all());
    }

    public function testIndexBy()
    {
        $collection = new TypedCollection([
            ['id' => 'user-1', 'value' => 'John Doe'],
            ['id' => 'user-2', 'value' => 'Jane Doe'],
        ], 'array');
        $indexed = $collection->indexBy('id');

        $this->assertInstanceOf(TypedCollection::class, $indexed);
        $this->assertArrayHasKey('user-1', $indexed->all());
        $this->assertArrayHasKey('user-2', $indexed->all());
    }

    public function testGroupBy()
    {
        $collection = new TypedCollection([
            ['id' => 'user-1', 'value' => 'John Doe', 'group' => 'group-1'],
            ['id' => 'user-2', 'value' => 'Jane Doe', 'group' => 'group-1'],
            ['id' => 'user-3', 'value' => 'Johnson Doe', 'group' => 'group-2'],
            ['id' => 'user-4', 'value' => 'Janifer Doe', 'group' => 'group-2'],
        ], 'array');

        $groupedCollection = $collection->groupBy('group');

        $this->assertArrayHasKey('group-1', $groupedCollection->all());
        $this->assertArrayHasKey('group-2', $groupedCollection->all());
        $this->assertInstanceOf(TypedCollection::class, $groupedCollection['group-1']);
        $this->assertInstanceOf(TypedCollection::class, $groupedCollection['group-2']);
    }

    public function testMap()
    {
        $collection = new TypedCollection([1, 2, 3], 'integer');
        $multiplied = $collection->map(static function ($item) {
            return $item * $item;
        });
        $this->assertNotEquals(TypedCollection::class, get_class($multiplied));
        $this->assertEquals([1, 4, 9], $multiplied->all());
    }

    public function testReplace()
    {
        $collection = new TypedCollection([1, 2, 3], 'integer');
        $this->assertEquals([1, 5, 3], $collection->replace(2, 5)->all());
    }

    public function testReplaceException()
    {
        $this->expectException(RuntimeException::class);
        $collection = new TypedCollection([1, 2, 3], 'integer');
        $collection->replace('2', '5', false);
    }

    public function testExtendTypedCollection()
    {
        $collection = new class([1, 2, 3]) extends TypedCollection
        {
            public function __construct($items = [])
            {
                parent::__construct($items, 'integer');
            }
        };
        $this->assertEquals([1, 5, 3], $collection->replace(2, 5)->all());
    }

    protected function getObjectsArray(int $times = 3): array
    {
        $prototype = new class extends \stdClass
        {
        };

        $items = [];
        while ($times > 0) {
            $items[] = clone $prototype;
            $times--;
        }

        return $items;
    }
}
