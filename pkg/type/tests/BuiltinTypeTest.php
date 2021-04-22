<?php

declare(strict_types=1);

namespace spaceonfire\Type;

use ArrayIterator;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class BuiltinTypeTest extends TestCase
{
    public function testConstructorFail(): void
    {
        $this->expectException(InvalidArgumentException::class);
        BuiltinType::new('unknown type');
    }

    public function testCheck(): void
    {
        $integer = BuiltinType::new(BuiltinType::INT);
        self::assertTrue($integer->check(1));
        self::assertFalse($integer->check('1'));

        $float = BuiltinType::new(BuiltinType::FLOAT);
        self::assertTrue($float->check(1.0));
        self::assertFalse($float->check('1'));

        $string = BuiltinType::new(BuiltinType::STRING);
        self::assertTrue($string->check('lorem ipsum'));
        self::assertFalse($string->check(1));

        $bool = BuiltinType::new(BuiltinType::BOOL);
        self::assertTrue($bool->check(true));
        self::assertFalse($bool->check(1));

        $object = BuiltinType::new(BuiltinType::OBJECT);
        self::assertTrue($object->check((object)[]));
        self::assertFalse($object->check(1));

        $array = BuiltinType::new(BuiltinType::ARRAY);
        self::assertTrue($array->check([]));
        self::assertFalse($array->check(1));

        $null = BuiltinType::new(BuiltinType::NULL);
        self::assertTrue($null->check(null));
        self::assertFalse($null->check(1));

        $callable = BuiltinType::new(BuiltinType::CALLABLE);
        self::assertTrue($callable->check(static function () {
        }));
        self::assertFalse($callable->check(1));

        $iterable = BuiltinType::new(BuiltinType::ITERABLE);
        self::assertTrue($iterable->check(new ArrayIterator()));
        self::assertFalse($iterable->check(1));

        $f = fopen(__FILE__, 'rb');
        $resource = BuiltinType::new(BuiltinType::RESOURCE);
        self::assertTrue($resource->check($f));
        self::assertFalse($resource->check(1));
        fclose($f);
    }

    public function testStringify(): void
    {
        $type = BuiltinType::new(BuiltinType::INT);
        self::assertSame(BuiltinType::INT, (string)$type);
    }

    public function testMagicFactory(): void
    {
        $int = BuiltinType::int();
        $intDuplicate = BuiltinType::new(BuiltinType::INT);

        self::assertSame($int, $intDuplicate);

        unset($int, $intDuplicate);
    }
}
