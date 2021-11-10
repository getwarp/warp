<?php

declare(strict_types=1);

namespace spaceonfire\Collection;

use PHPUnit\Framework\TestCase;
use spaceonfire\Type\BuiltinType;

class MapTest extends TestCase
{
    public function testNewFromArray(): void
    {
        $range = \range(0, 8);
        $chars = \array_map(static fn ($i) => \chr($i), $range);
        $array = \array_combine($chars, $range);

        $map = Map::new($array);

        self::assertSame($array, \iterator_to_array($map));
    }

    public function testNewWithValueType(): void
    {
        $this->expectException(\LogicException::class);

        \iterator_to_array(Map::new(\range(0, 8), BuiltinType::string()));
    }

    public function testNewFromOtherMap(): void
    {
        $range = \range(0, 8);

        $map = Map::new($range);

        self::assertSame($map, Map::new($map));
    }

    public function testSet(): void
    {
        $map = Map::new();

        $map->set('key', 'value');

        self::assertSame([
            'key' => 'value',
        ], \iterator_to_array($map));
    }

    public function testSetInvalidKey(): void
    {
        $this->expectException(\LogicException::class);

        $map = Map::new();

        $map->set(true, 1);
    }

    public function testSetInvalidValue(): void
    {
        $this->expectException(\LogicException::class);

        $map = Map::new([], BuiltinType::string());

        $map->set('key', 1);
    }

    public function testUnset(): void
    {
        $map = Map::new([
            'key1' => 'value1',
            'key2' => 'value2',
        ]);

        $map->unset('key1');

        self::assertSame([
            'key2' => 'value2',
        ], \iterator_to_array($map));
    }

    public function testUnsetNothing(): void
    {
        $map = Map::new([
            'key1' => 'value1',
            'key2' => 'value2',
        ]);

        $map->unset('key3');

        self::assertSame([
            'key1' => 'value1',
            'key2' => 'value2',
        ], \iterator_to_array($map));
    }

    public function testUnsetInvalidKey(): void
    {
        $this->expectException(\LogicException::class);

        $map = Map::new();

        $map->unset(null);
    }

    public function testHas(): void
    {
        $map = Map::new([
            'key1' => 'value1',
            'key2' => 'value2',
        ]);

        self::assertTrue($map->has('key1'));
        self::assertFalse($map->has('key3'));
    }

    public function testHasInvalidKey(): void
    {
        $this->expectException(\LogicException::class);

        $map = Map::new([
            'key1' => 'value1',
            'key2' => 'value2',
        ]);

        $map->has([]);
    }

    public function testGet(): void
    {
        $map = Map::new([
            'key1' => 'value1',
            'key2' => 'value2',
        ]);

        self::assertSame('value1', $map->get('key1'));
        self::assertNull($map->get('key3'));
    }

    public function testGetInvalidKey(): void
    {
        $this->expectException(\LogicException::class);

        $map = Map::new([
            'key1' => 'value1',
            'key2' => 'value2',
        ]);

        $map->get(false);
    }

    public function testValues(): void
    {
        $map = Map::new([
            'key1' => 'value1',
            'key2' => 'value2',
        ]);

        $values = $map->values();

        self::assertSame(['value1', 'value2'], \iterator_to_array($values));
    }

    public function testKeys(): void
    {
        $map = Map::new([
            'key1' => 'value1',
            'key2' => 'value2',
        ]);

        $keys = $map->keys();

        self::assertSame(['key1', 'key2'], \iterator_to_array($keys));
    }

    public function testFirstKey(): void
    {
        $map = Map::new([
            'key1' => 'value1',
            'key2' => 'value2',
        ]);

        self::assertSame('key1', $map->firstKey());
    }

    public function testFirstKeyEmpty(): void
    {
        $map = Map::new();

        self::assertNull($map->firstKey());
    }

    public function testLastKey(): void
    {
        $map = Map::new([
            'key1' => 'value1',
            'key2' => 'value2',
        ]);

        self::assertSame('key2', $map->lastKey());
    }

    public function testLastKeyEmpty(): void
    {
        $map = Map::new();

        self::assertNull($map->lastKey());
    }

    public function testMerge(): void
    {
        $map = Map::new([
            'key1' => 'value1',
            'key2' => 'value2',
        ]);

        $merged = $map->merge(
            [
                'key3' => 'value3',
            ],
            (static fn () => yield 'key3' => 'value3')(),
            (static fn () => yield 'key4' => 'value4')(),
            Map::new([
                'key5' => 'value5',
            ]),
        );

        self::assertNotSame($map, $merged);
        self::assertSame([
            'key1' => 'value1',
            'key2' => 'value2',
            'key3' => 'value3',
            'key4' => 'value4',
            'key5' => 'value5',
        ], \iterator_to_array($merged));
    }

    public function testCount(): void
    {
        $map = Map::new([
            'key1' => 'value1',
            'key2' => 'value2',
        ]);

        self::assertCount(2, $map);

        $map->set('key3', 'value3');

        self::assertCount(3, $map);
    }

    public function testJsonSerialize(): void
    {
        $array = [
            'key1' => 'value1',
            'key2' => 'value2',
        ];

        $map = Map::new($array);

        self::assertJsonStringEqualsJsonString(
            \json_encode($array, JSON_THROW_ON_ERROR),
            \json_encode($map, JSON_THROW_ON_ERROR)
        );
    }

    public function testIterateAndUnset(): void
    {
        $map = Map::new([
            'value1' => 'value1',
            'value2' => 'value2',
        ]);

        foreach ($map->values() as $value) {
            $map->unset($value);
        }

        self::assertCount(0, $map);
    }
}
