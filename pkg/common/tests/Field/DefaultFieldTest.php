<?php

declare(strict_types=1);

namespace spaceonfire\Common\Field;

use PHPUnit\Framework\TestCase;

class DefaultFieldTest extends TestCase
{
    public function testDefault(): void
    {
        $field = new DefaultField('name');

        self::assertSame('name', (string)$field);
        self::assertSame(['name'], $field->getElements());

        $array = [
            'name' => 'John Doe',
        ];

        $arrayAccess = new \ArrayObject($array);
        $object = (object)$array;

        self::assertSame('John Doe', $field->extract($array));
        self::assertSame('John Doe', $field->extract($arrayAccess));
        self::assertSame('John Doe', $field->extract($object));
        self::assertNull($field->extract(null));
    }

    public function testNestedDot(): void
    {
        $field = new DefaultField('user.name');

        self::assertSame('user.name', (string)$field);
        self::assertSame(['user', 'name'], $field->getElements());

        $array = [
            'user' => [
                'name' => 'John Doe',
            ],
        ];

        $arrayAccess = new \ArrayObject($array);
        $object = (object)$array;
        $object->user = (object)$object->user;

        self::assertSame('John Doe', $field->extract($array));
        self::assertSame('John Doe', $field->extract($arrayAccess));
        self::assertSame('John Doe', $field->extract($object));
        self::assertNull($field->extract(null));
    }

    public function testNestedArrayKey(): void
    {
        $field = new DefaultField('user[name]');

        self::assertSame('user[name]', (string)$field);
        self::assertSame(['user', 'name'], $field->getElements());

        $array = [
            'user' => [
                'name' => 'John Doe',
            ],
        ];

        $arrayAccess = new \ArrayObject($array);
        $object = (object)$array;
        $object->user = (object)$object->user;

        self::assertSame('John Doe', $field->extract($array));
        self::assertSame('John Doe', $field->extract($arrayAccess));
        self::assertSame('John Doe', $field->extract($object));
        self::assertNull($field->extract(null));
    }

    public function testEmptyStringNotAllowed(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new DefaultField('');
    }

    /**
     * @dataProvider invalidFieldsProvider
     * @param string $field
     */
    public function testInvalidField(string $field): void
    {
        $this->expectException(\LogicException::class);
        new DefaultField($field);
    }

    public function invalidFieldsProvider(): \Generator
    {
        yield ['[]'];
        yield ['.'];
        yield ['name.'];
        yield ['[name'];
    }
}
