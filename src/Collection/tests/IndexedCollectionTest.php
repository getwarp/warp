<?php

declare(strict_types=1);

namespace spaceonfire\Collection;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class IndexedCollectionTest extends TestCase
{
    public function testConstructStringIndexer()
    {
        $collection = new IndexedCollection([
            ['id' => 'user-1', 'value' => 'John Doe'],
            ['id' => 'user-2', 'value' => 'Jane Doe'],
            ['id' => 'user-3', 'value' => 'Johnson Doe'],
            ['id' => 'user-4', 'value' => 'Janifer Doe'],
        ], 'id');

        self::assertEquals(['user-1', 'user-2', 'user-3', 'user-4'], $collection->keys()->all());
    }

    public function testConstructCallableIndexer()
    {
        $collection = new IndexedCollection([
            ['id' => 'user-1', 'value' => 'John Doe'],
            ['id' => 'user-2', 'value' => 'Jane Doe'],
            ['id' => 'user-3', 'value' => 'Johnson Doe'],
            ['id' => 'user-4', 'value' => 'Janifer Doe'],
        ], static function ($item) {
            static $counter = 0;
            return $item['id'] . ++$counter;
        });

        self::assertEquals(['user-11', 'user-22', 'user-33', 'user-44'], $collection->keys()->all());
    }

    public function testConstructFail()
    {
        $this->expectException(InvalidArgumentException::class);
        new IndexedCollection([], null);
    }

    public function testOffsetSet()
    {
        $collection = new IndexedCollection([], 'id');
        $collection[] = ['id' => 'user-1', 'value' => 'John Doe'];
        self::assertTrue($collection->offsetExists('user-1'));
    }

    public function testMerge()
    {
        $collectionA = new IndexedCollection([
            ['id' => 'user-1', 'value' => 'John Doe'],
            ['id' => 'user-2', 'value' => 'Jane Doe'],
        ], 'id');

        $collectionB = new Collection([
            ['id' => 'user-3', 'value' => 'Johnson Doe'],
            ['id' => 'user-4', 'value' => 'Janifer Doe'],
        ]);

        $collectionC = $collectionA->merge($collectionB);
        self::assertEquals(['user-1', 'user-2', 'user-3', 'user-4'], $collectionC->keys()->all());
    }

    public function testRemap()
    {
        $collection = new IndexedCollection([
            ['id' => 'user-1', 'value' => 'John Doe'],
            ['id' => 'user-2', 'value' => 'Jane Doe'],
        ], 'id');
        self::assertEquals([
            'user-1' => 'John Doe',
            'user-2' => 'Jane Doe',
        ], $collection->remap('id', 'value')->all());
    }

    public function testFlip()
    {
        $collection = new IndexedCollection([1, 2, 3], static function () {
            static $i = 100;
            return $i--;
        });

        self::assertEquals([1 => 100, 2 => 99, 3 => 98], $collection->flip()->all());
    }
}
