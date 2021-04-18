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
        new BuiltinType('unknown type');
    }

    public function testConstructorNonStrictNotice(): void
    {
        $this->expectNotice();
        new BuiltinType(BuiltinType::OBJECT, false);
    }

    public function testCheck(): void
    {
        $integer = new BuiltinType(BuiltinType::INT);
        self::assertTrue($integer->check(1));
        self::assertFalse($integer->check('1'));

        $float = new BuiltinType(BuiltinType::FLOAT);
        self::assertTrue($float->check(1.0));
        self::assertFalse($float->check('1'));

        $string = new BuiltinType(BuiltinType::STRING);
        self::assertTrue($string->check('lorem ipsum'));
        self::assertFalse($string->check(1));

        $bool = new BuiltinType(BuiltinType::BOOL);
        self::assertTrue($bool->check(true));
        self::assertFalse($bool->check(1));

        $object = new BuiltinType(BuiltinType::OBJECT);
        self::assertTrue($object->check((object)[]));
        self::assertFalse($object->check(1));

        $array = new BuiltinType(BuiltinType::ARRAY);
        self::assertTrue($array->check([]));
        self::assertFalse($array->check(1));

        $null = new BuiltinType(BuiltinType::NULL);
        self::assertTrue($null->check(null));
        self::assertFalse($null->check(1));

        $callable = new BuiltinType(BuiltinType::CALLABLE);
        self::assertTrue($callable->check(static function () {
        }));
        self::assertFalse($callable->check(1));

        $iterable = new BuiltinType(BuiltinType::ITERABLE);
        self::assertTrue($iterable->check(new ArrayIterator()));
        self::assertFalse($iterable->check(1));

        $f = fopen(__FILE__, 'rb');
        $resource = new BuiltinType(BuiltinType::RESOURCE);
        self::assertTrue($resource->check($f));
        self::assertFalse($resource->check(1));
        fclose($f);
    }

    public function testNonStrictCheck(): void
    {
        $integer = new BuiltinType(BuiltinType::INT, false);
        self::assertTrue($integer->check(1));
        self::assertTrue($integer->check('1'));
        self::assertFalse($integer->check('lorem ipsum'));

        $float = new BuiltinType(BuiltinType::FLOAT, false);
        self::assertTrue($float->check(1.0));
        self::assertTrue($float->check('1.1'));
        self::assertFalse($float->check('lorem ipsum'));

        $string = new BuiltinType(BuiltinType::STRING, false);
        self::assertTrue($string->check('lorem ipsum'));
        self::assertTrue($string->check(1));
        self::assertTrue($string->check(true));
        self::assertTrue($string->check(new class {
            public function __toString(): string
            {
                return 'lorem ipsum';
            }
        }));
        self::assertFalse($string->check(null));
        self::assertFalse($string->check([]));

        $bool = new BuiltinType(BuiltinType::BOOL, false);
        self::assertTrue($bool->check(true));
        self::assertTrue($bool->check(false));
        self::assertTrue($bool->check(1));
        self::assertTrue($bool->check(''));
        self::assertFalse($bool->check([]));
        self::assertFalse($bool->check((object)[]));
        self::assertFalse($bool->check(null));
    }

    public function testCast(): void
    {
        $integer = new BuiltinType(BuiltinType::INT);
        self::assertSame(1, $integer->cast('1'));

        $float = new BuiltinType(BuiltinType::FLOAT);
        self::assertSame(1.0, $float->cast('1.0'));

        $string = new BuiltinType(BuiltinType::STRING);
        self::assertSame('lorem ipsum', $string->cast(new class {
            public function __toString(): string
            {
                return 'lorem ipsum';
            }
        }));

        $bool = new BuiltinType(BuiltinType::BOOL);
        self::assertTrue($bool->cast(true));
        self::assertTrue($bool->cast('1'));
        self::assertTrue($bool->cast(1));
        self::assertTrue($bool->cast(-2));
        self::assertFalse($bool->cast(false));
        self::assertFalse($bool->cast(''));
        self::assertFalse($bool->cast(0));
        self::assertFalse($bool->cast(-0.0));

        $null = new BuiltinType(BuiltinType::NULL);
        self::assertNull($null->cast(1));

        $noCast = new BuiltinType(BuiltinType::RESOURCE);
        self::assertSame(1, $noCast->cast(1));
    }

    public function testStringify(): void
    {
        $type = new BuiltinType(BuiltinType::INT);
        self::assertSame(BuiltinType::INT, (string)$type);
    }
}
