<?php

declare(strict_types=1);

namespace spaceonfire\Type;

use ArrayIterator;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use stdClass;

class BuiltinTypeTest extends TestCase
{
    public function testSupports(): void
    {
        self::assertTrue(BuiltinType::supports('int'));
        self::assertTrue(BuiltinType::supports('integer'));
        self::assertTrue(BuiltinType::supports('bool'));
        self::assertTrue(BuiltinType::supports('boolean'));
        self::assertTrue(BuiltinType::supports('float'));
        self::assertTrue(BuiltinType::supports('double'));
        self::assertTrue(BuiltinType::supports('string'));
        self::assertTrue(BuiltinType::supports('resource'));
        self::assertTrue(BuiltinType::supports('resource (closed)'));
        self::assertTrue(BuiltinType::supports('null'));
        self::assertTrue(BuiltinType::supports('object'));
        self::assertTrue(BuiltinType::supports('array'));
        self::assertTrue(BuiltinType::supports('callable'));
        self::assertTrue(BuiltinType::supports('iterable'));
        self::assertFalse(BuiltinType::supports('unknown'));
        self::assertFalse(BuiltinType::supports(stdClass::class));
    }

    public function testCreate(): void
    {
        BuiltinType::create('integer');
        self::assertTrue(true);
    }

    public function testCreateFail(): void
    {
        $this->expectException(InvalidArgumentException::class);
        BuiltinType::create('unknown type');
    }

    public function testCheck(): void
    {
        $integer = BuiltinType::create('integer');
        self::assertTrue($integer->check(1));
        self::assertFalse($integer->check('1'));

        $float = BuiltinType::create('float');
        self::assertTrue($float->check(1.0));
        self::assertFalse($float->check('1'));

        $string = BuiltinType::create('string');
        self::assertTrue($string->check('lorem ipsum'));
        self::assertFalse($string->check(1));

        $bool = BuiltinType::create('bool');
        self::assertTrue($bool->check(true));
        self::assertFalse($bool->check(1));

        $object = BuiltinType::create('object');
        self::assertTrue($object->check((object)[]));
        self::assertFalse($object->check(1));

        $array = BuiltinType::create('array');
        self::assertTrue($array->check([]));
        self::assertFalse($array->check(1));

        $null = BuiltinType::create('null');
        self::assertTrue($null->check(null));
        self::assertFalse($null->check(1));

        $callable = BuiltinType::create('callable');
        self::assertTrue($callable->check(static function () {
        }));
        self::assertFalse($callable->check(1));

        $iterable = BuiltinType::create('iterable');
        self::assertTrue($iterable->check(new ArrayIterator()));
        self::assertFalse($iterable->check(1));

        $f = fopen(__FILE__, 'rb');
        $resource = BuiltinType::create('resource');
        self::assertTrue($resource->check($f));
        self::assertFalse($resource->check(1));
        fclose($f);
    }

    public function testNonStrictCheck(): void
    {
        $integer = BuiltinType::create('integer', false);
        self::assertTrue($integer->check(1));
        self::assertTrue($integer->check('1'));
        self::assertFalse($integer->check('lorem ipsum'));

        $float = BuiltinType::create('float', false);
        self::assertTrue($float->check(1.0));
        self::assertTrue($float->check('1.1'));
        self::assertFalse($float->check('lorem ipsum'));

        $string = BuiltinType::create('string', false);
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

        $bool = BuiltinType::create('bool', false);
        self::assertTrue($bool->check(true));
        self::assertTrue($bool->check(false));
        self::assertTrue($bool->check(1));
        self::assertTrue($bool->check(''));
        self::assertFalse($bool->check([]));
        self::assertFalse($bool->check((object)[]));
        self::assertFalse($bool->check(null));
    }

    public function testNonStrictNotice(): void
    {
        $this->expectNotice();
        BuiltinType::create('object', false);
    }

    public function testCast(): void
    {
        $integer = BuiltinType::create('integer');
        self::assertSame(1, $integer->cast('1'));

        $float = BuiltinType::create('float');
        self::assertSame(1.0, $float->cast('1.0'));

        $string = BuiltinType::create('string');
        self::assertSame('lorem ipsum', $string->cast(new class {
            public function __toString(): string
            {
                return 'lorem ipsum';
            }
        }));

        $bool = BuiltinType::create('bool');
        self::assertTrue($bool->cast(true));
        self::assertTrue($bool->cast('1'));
        self::assertTrue($bool->cast(1));
        self::assertTrue($bool->cast(-2));
        self::assertFalse($bool->cast(false));
        self::assertFalse($bool->cast(''));
        self::assertFalse($bool->cast(0));
        self::assertFalse($bool->cast(-0.0));

        $null = BuiltinType::create('null');
        self::assertNull($null->cast(1));

        $noCast = BuiltinType::create('resource');
        self::assertSame(1, $noCast->cast(1));
    }

    public function testStringify(): void
    {
        $type = BuiltinType::create('integer');
        self::assertSame('int', (string)$type);
    }
}
